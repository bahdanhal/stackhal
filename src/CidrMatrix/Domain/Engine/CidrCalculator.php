<?php

declare(strict_types=1);

namespace App\CidrMatrix\Domain\Engine;

use App\CidrMatrix\Domain\Model\CidrBlock;
use App\CidrMatrix\Domain\Model\CidrDiagnostic;
use App\CidrMatrix\Domain\Model\CidrMatrixResult;
use App\CidrMatrix\Domain\Model\IpVersion;
use App\CidrMatrix\Domain\Model\SubnetCollision;

final readonly class CidrCalculator
{
    private const int MAX_SUBNETS_PER_REQUEST = 100;

    /**
     * @param list<string> $cidrInputs
     */
    public function analyze(
        array $cidrInputs,
        ?int $requestedFreePrefix = null,
        ?string $parentCidrInput = null,
    ): CidrMatrixResult {
        $parsedBlocks = [];
        $diagnostics = [];
        $warnings = [];

        $inputCount = 0;
        foreach ($cidrInputs as $rawInput) {
            $rawInput = trim($rawInput);
            if ($rawInput === '') {
                continue;
            }

            if ($inputCount >= self::MAX_SUBNETS_PER_REQUEST) {
                break;
            }

            $block = CidrBlock::parse($rawInput);
            if ($block === null) {
                $diagnostics[] = new CidrDiagnostic(
                    code: 'ERR_INVALID_CIDR',
                    severity: 'error',
                    title: 'Invalid CIDR Format',
                    description: sprintf('Provided string "%s" is not a valid IPv4 or IPv6 CIDR notation.', $rawInput),
                    context: $rawInput
                );
                continue;
            }

            $inputCount++;
            $parsedBlocks[] = $block;

            if (!$block->isCanonical) {
                if (!in_array('WARN_NON_CANONICAL_IP', $warnings, true)) {
                    $warnings[] = 'WARN_NON_CANONICAL_IP';
                }
                $diagnostics[] = new CidrDiagnostic(
                    code: 'WARN_NON_CANONICAL_IP',
                    severity: 'warning',
                    title: 'Host Bits Set in Network Address',
                    description: sprintf(
                        'IP address "%s" contained host bits for prefix /%d; normalized to "%s".',
                        $block->ipAddress,
                        $block->prefixLength,
                        $block->normalizedCidr
                    ),
                    context: $block->rawInput
                );
            }
        }

        // Check collisions and containments
        $collisions = [];
        $containments = [];
        $count = count($parsedBlocks);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $blockA = $parsedBlocks[$i];
                $blockB = $parsedBlocks[$j];

                if ($blockA->version !== $blockB->version) {
                    continue;
                }

                if ($blockA->overlaps($blockB)) {
                    $overlapStart = max($blockA->startBytes, $blockB->startBytes);
                    $overlapEnd = min($blockA->endBytes, $blockB->endBytes);

                    $startIp = inet_ntop($overlapStart);
                    $endIp = inet_ntop($overlapEnd);

                    if ($startIp !== false && $endIp !== false) {
                        $overlapCidr = $this->calculateOverlapCidr(
                            $overlapStart,
                            $overlapEnd,
                            $blockA->version,
                            $blockA,
                            $blockB
                        );

                        $collisions[] = new SubnetCollision(
                            cidrA: $blockA->normalizedCidr,
                            cidrB: $blockB->normalizedCidr,
                            overlapCidr: $overlapCidr,
                            startIp: $startIp,
                            endIp: $endIp,
                        );

                        if (!in_array('ERR_OVERLAP_COLLISION', $warnings, true)) {
                            $warnings[] = 'ERR_OVERLAP_COLLISION';
                        }

                        $diagnostics[] = new CidrDiagnostic(
                            code: 'ERR_OVERLAP_COLLISION',
                            severity: 'error',
                            title: 'Subnet Overlap Collision',
                            description: sprintf(
                                'Subnets %s and %s overlap in address range %s - %s.',
                                $blockA->normalizedCidr,
                                $blockB->normalizedCidr,
                                $startIp,
                                $endIp
                            ),
                            context: sprintf('%s <-> %s', $blockA->normalizedCidr, $blockB->normalizedCidr)
                        );
                    }
                }

                if ($blockA->contains($blockB) && $blockA->normalizedCidr !== $blockB->normalizedCidr) {
                    $containments[] = [
                        'parent' => $blockA->normalizedCidr,
                        'child' => $blockB->normalizedCidr,
                    ];
                    if (!in_array('WARN_CONTAINMENT', $warnings, true)) {
                        $warnings[] = 'WARN_CONTAINMENT';
                    }
                    $diagnostics[] = new CidrDiagnostic(
                        code: 'WARN_CONTAINMENT',
                        severity: 'warning',
                        title: 'Parent-Child Containment',
                        description: sprintf('%s completely encompasses smaller subnet %s.', $blockA->normalizedCidr, $blockB->normalizedCidr),
                        context: sprintf('%s > %s', $blockA->normalizedCidr, $blockB->normalizedCidr)
                    );
                } elseif ($blockB->contains($blockA) && $blockB->normalizedCidr !== $blockA->normalizedCidr) {
                    $containments[] = [
                        'parent' => $blockB->normalizedCidr,
                        'child' => $blockA->normalizedCidr,
                    ];
                    if (!in_array('WARN_CONTAINMENT', $warnings, true)) {
                        $warnings[] = 'WARN_CONTAINMENT';
                    }
                    $diagnostics[] = new CidrDiagnostic(
                        code: 'WARN_CONTAINMENT',
                        severity: 'warning',
                        title: 'Parent-Child Containment',
                        description: sprintf('%s completely encompasses smaller subnet %s.', $blockB->normalizedCidr, $blockA->normalizedCidr),
                        context: sprintf('%s > %s', $blockB->normalizedCidr, $blockA->normalizedCidr)
                    );
                }
            }
        }

        // Free Subnet Allocation logic
        $parentBlock = $parentCidrInput !== null ? CidrBlock::parse($parentCidrInput) : null;
        $freeSubnetCidr = null;
        if ($requestedFreePrefix !== null) {
            $allocParentBlock = $parentBlock ?? ($parsedBlocks[0] ?? null);

            if ($allocParentBlock !== null && $requestedFreePrefix >= $allocParentBlock->prefixLength) {
                $freeSubnetCidr = $this->findFreeSubnet(
                    $allocParentBlock,
                    $requestedFreePrefix,
                    $parsedBlocks
                );

                if ($freeSubnetCidr !== null) {
                    $diagnostics[] = new CidrDiagnostic(
                        code: 'INFO_FREE_ALLOCATION_FOUND',
                        severity: 'info',
                        title: 'Free Allocation Available',
                        description: sprintf(
                            'Found available free subnet %s matching requested prefix /%d.',
                            $freeSubnetCidr,
                            $requestedFreePrefix
                        ),
                        context: $freeSubnetCidr
                    );
                } else {
                    $diagnostics[] = new CidrDiagnostic(
                        code: 'ERR_SUBNET_EXHAUSTED',
                        severity: 'error',
                        title: 'Subnet Space Exhausted',
                        description: sprintf(
                            'No contiguous free subnet of prefix /%d available inside %s.',
                            $requestedFreePrefix,
                            $allocParentBlock->normalizedCidr
                        ),
                        context: (string) $requestedFreePrefix
                    );
                }
            }
        }

        // Matrix Grid, Pairwise Cross-Matrix, Space Partitions, and Hierarchy Tree
        $matrixGrid = $this->buildMatrixGrid($parsedBlocks, $collisions);
        $pairwiseMatrix = $this->buildPairwiseMatrix($parsedBlocks);
        $spacePartitions = $this->buildSpacePartitions($parsedBlocks, $collisions, $parentBlock, $freeSubnetCidr);
        $treeNodes = $this->buildTreeNodes($parsedBlocks, $parentBlock, $freeSubnetCidr);

        return new CidrMatrixResult(
            parsedBlocks: $parsedBlocks,
            hasCollisions: count($collisions) > 0,
            collisionCount: count($collisions),
            collisions: $collisions,
            containments: $containments,
            freeSubnetCidr: $freeSubnetCidr,
            matrixGrid: $matrixGrid,
            diagnostics: $diagnostics,
            warnings: array_values(array_unique($warnings)),
            pairwiseMatrix: $pairwiseMatrix,
            spacePartitions: $spacePartitions,
            treeNodes: $treeNodes,
        );
    }

    private function calculateOverlapCidr(
        string $overlapStart,
        string $overlapEnd,
        IpVersion $version,
        CidrBlock $blockA,
        CidrBlock $blockB
    ): string {
        // If one contains the other, overlap CIDR is the smaller block
        if ($blockA->contains($blockB)) {
            return $blockB->normalizedCidr;
        }
        if ($blockB->contains($blockA)) {
            return $blockA->normalizedCidr;
        }

        $startIp = inet_ntop($overlapStart);
        $endIp = inet_ntop($overlapEnd);

        if ($startIp === false || $endIp === false) {
            return 'unknown';
        }

        return sprintf('%s - %s', $startIp, $endIp);
    }

    /**
     * @param list<CidrBlock> $existingBlocks
     */
    private function findFreeSubnet(
        CidrBlock $parent,
        int $requestedPrefix,
        array $existingBlocks
    ): ?string {
        $currBytes = $parent->startBytes;
        $maxAttempts = 256;
        $attempts = 0;

        while ($currBytes <= $parent->endBytes && $attempts < $maxAttempts) {
            $attempts++;
            $candidateIp = inet_ntop($currBytes);
            if ($candidateIp === false) {
                break;
            }

            $candidateCidrString = sprintf('%s/%d', $candidateIp, $requestedPrefix);
            $candidateBlock = CidrBlock::parse($candidateCidrString);
            if ($candidateBlock === null || !$parent->contains($candidateBlock)) {
                break;
            }

            $hasConflict = false;
            foreach ($existingBlocks as $existing) {
                if ($existing->overlaps($candidateBlock)) {
                    $hasConflict = true;
                    break;
                }
            }

            if (!$hasConflict) {
                return $candidateBlock->normalizedCidr;
            }

            $nextBytes = $this->addBlockSize($currBytes, $requestedPrefix, $parent->version);
            if ($nextBytes === null || $nextBytes <= $currBytes) {
                break;
            }
            $currBytes = $nextBytes;
        }

        return null;
    }

    private function addBlockSize(string $binaryBytes, int $prefixLength, IpVersion $version): ?string
    {
        if ($version === IpVersion::V4) {
            $unpacked = unpack('N', $binaryBytes);
            if ($unpacked === false) {
                return null;
            }
            $val = $unpacked[1];
            $hostBits = 32 - $prefixLength;
            if ($hostBits >= 32) {
                return null;
            }
            $step = 1 << $hostBits;
            $next = $val + $step;
            if ($next > 0xFFFFFFFF) {
                return null;
            }
            return pack('N', $next);
        }

        $unpacked = unpack('C*', $binaryBytes);
        if ($unpacked === false) {
            return null;
        }
        $bytes = array_values($unpacked);
        $byteIndex = (int) floor(($prefixLength - 1) / 8);
        if ($byteIndex < 0) {
            $byteIndex = 0;
        }
        $bitOffset = ($prefixLength - 1) % 8;
        $increment = 1 << (7 - $bitOffset);

        $carry = $increment;
        for ($i = $byteIndex; $i >= 0; $i--) {
            $sum = $bytes[$i] + $carry;
            $bytes[$i] = $sum & 0xFF;
            $carry = $sum >> 8;
            if ($carry === 0) {
                break;
            }
        }
        if ($carry > 0) {
            return null;
        }

        return pack('C*', ...$bytes);
    }

    /**
     * @param list<CidrBlock> $blocks
     * @param list<SubnetCollision> $collisions
     * @return list<array<string, mixed>>
     */
    private function buildMatrixGrid(array $blocks, array $collisions): array
    {
        if (count($blocks) === 0) {
            return [];
        }

        $grid = [];
        foreach ($blocks as $idx => $block) {
            $hasCollision = false;
            foreach ($collisions as $col) {
                if ($col->cidrA === $block->normalizedCidr || $col->cidrB === $block->normalizedCidr) {
                    $hasCollision = true;
                    break;
                }
            }

            $grid[] = [
                'id' => sprintf('block-%d', $idx + 1),
                'cidr' => $block->normalizedCidr,
                'raw_input' => $block->rawInput,
                'version' => $block->version->value,
                'network_ip' => $block->networkIp,
                'broadcast_ip' => $block->broadcastIp,
                'prefix_length' => $block->prefixLength,
                'total_hosts' => $block->totalHosts,
                'status' => $hasCollision ? 'collision' : 'assigned',
            ];
        }

        return $grid;
    }

    /**
     * @param list<CidrBlock> $blocks
     * @return array<string, mixed>
     */
    private function buildPairwiseMatrix(array $blocks): array
    {
        if (count($blocks) === 0) {
            return [
                'headers' => [],
                'rows' => [],
                'total_count' => 0,
            ];
        }

        $headers = [];
        foreach ($blocks as $idx => $b) {
            $headers[] = [
                'index' => $idx,
                'id' => sprintf('block-%d', $idx + 1),
                'cidr' => $b->normalizedCidr,
                'version' => $b->version->value,
                'label' => sprintf('#%d %s', $idx + 1, $b->normalizedCidr),
            ];
        }

        $rows = [];
        foreach ($blocks as $rowIdx => $blockA) {
            $cells = [];
            foreach ($blocks as $colIdx => $blockB) {
                if ($rowIdx === $colIdx) {
                    $cells[] = [
                        'row_idx' => $rowIdx,
                        'col_idx' => $colIdx,
                        'relation' => 'self',
                        'status' => 'self',
                        'has_collision' => false,
                        'badge' => 'SELF',
                        'label' => 'Identity',
                        'description' => sprintf('Subnet #%d (%s)', $rowIdx + 1, $blockA->normalizedCidr),
                    ];
                    continue;
                }

                if ($blockA->version !== $blockB->version) {
                    $cells[] = [
                        'row_idx' => $rowIdx,
                        'col_idx' => $colIdx,
                        'relation' => 'different_family',
                        'status' => 'disjoint',
                        'has_collision' => false,
                        'badge' => 'DISJOINT',
                        'label' => 'Separate AF',
                        'description' => 'Different IP address families (IPv4 vs IPv6)',
                    ];
                    continue;
                }

                if ($blockA->normalizedCidr === $blockB->normalizedCidr) {
                    $cells[] = [
                        'row_idx' => $rowIdx,
                        'col_idx' => $colIdx,
                        'relation' => 'duplicate',
                        'status' => 'collision',
                        'has_collision' => true,
                        'badge' => 'COLLISION',
                        'label' => 'Duplicate Subnet',
                        'description' => sprintf('Exact duplicate allocation: %s', $blockA->normalizedCidr),
                    ];
                    continue;
                }

                if ($blockA->contains($blockB)) {
                    $cells[] = [
                        'row_idx' => $rowIdx,
                        'col_idx' => $colIdx,
                        'relation' => 'contains',
                        'status' => 'containment',
                        'has_collision' => true,
                        'badge' => 'CONTAINMENT',
                        'label' => 'Encloses B',
                        'description' => sprintf('%s encompasses %s', $blockA->normalizedCidr, $blockB->normalizedCidr),
                    ];
                    continue;
                }

                if ($blockB->contains($blockA)) {
                    $cells[] = [
                        'row_idx' => $rowIdx,
                        'col_idx' => $colIdx,
                        'relation' => 'contained_by',
                        'status' => 'containment',
                        'has_collision' => true,
                        'badge' => 'CONTAINMENT',
                        'label' => 'Inside B',
                        'description' => sprintf('%s is enclosed within %s', $blockA->normalizedCidr, $blockB->normalizedCidr),
                    ];
                    continue;
                }

                if ($blockA->overlaps($blockB)) {
                    $cells[] = [
                        'row_idx' => $rowIdx,
                        'col_idx' => $colIdx,
                        'relation' => 'overlap',
                        'status' => 'collision',
                        'has_collision' => true,
                        'badge' => 'COLLISION',
                        'label' => 'Direct Collision',
                        'description' => sprintf('Overlapping range between %s and %s', $blockA->normalizedCidr, $blockB->normalizedCidr),
                    ];
                    continue;
                }

                $cells[] = [
                    'row_idx' => $rowIdx,
                    'col_idx' => $colIdx,
                    'relation' => 'disjoint',
                    'status' => 'disjoint',
                    'has_collision' => false,
                    'badge' => 'DISJOINT',
                    'label' => 'Disjoint (OK)',
                    'description' => sprintf('Clean separation between %s and %s', $blockA->normalizedCidr, $blockB->normalizedCidr),
                ];
            }

            $rows[] = [
                'index' => $rowIdx,
                'id' => sprintf('block-%d', $rowIdx + 1),
                'cidr' => $blockA->normalizedCidr,
                'label' => sprintf('#%d %s', $rowIdx + 1, $blockA->normalizedCidr),
                'cells' => $cells,
            ];
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
            'total_count' => count($blocks),
        ];
    }

    /**
     * @param list<CidrBlock> $blocks
     * @param list<SubnetCollision> $collisions
     * @return list<array<string, mixed>>
     */
    private function buildSpacePartitions(
        array $blocks,
        array $collisions,
        ?CidrBlock $parentBlock,
        ?string $freeSubnetCidr
    ): array {
        if (count($blocks) === 0) {
            return [];
        }

        $partitions = [];
        $freeBlock = $freeSubnetCidr !== null ? CidrBlock::parse($freeSubnetCidr) : null;

        $v4Blocks = array_values(array_filter($blocks, static fn (CidrBlock $b) => $b->version === IpVersion::V4));
        $v6Blocks = array_values(array_filter($blocks, static fn (CidrBlock $b) => $b->version === IpVersion::V6));

        /**
         * @param list<CidrBlock> $groupBlocks
         */
        $processGroup = function (array $groupBlocks, IpVersion $version) use ($collisions, $parentBlock, $freeBlock, &$partitions): void {
            if (count($groupBlocks) === 0) {
                return;
            }

            $scope = null;
            if ($parentBlock !== null && $parentBlock->version === $version) {
                $scope = $parentBlock;
            } else {
                $scope = $this->findEnclosingSupernet(array_values($groupBlocks), $version);
            }

            foreach ($groupBlocks as $idx => $block) {
                $conflictingCidrs = [];
                foreach ($collisions as $col) {
                    if ($col->cidrA === $block->normalizedCidr && $col->cidrB !== $block->normalizedCidr) {
                        $conflictingCidrs[] = $col->cidrB;
                    } elseif ($col->cidrB === $block->normalizedCidr && $col->cidrA !== $block->normalizedCidr) {
                        $conflictingCidrs[] = $col->cidrA;
                    }
                }
                $conflictingCidrs = array_values(array_unique($conflictingCidrs));
                $hasCollision = count($conflictingCidrs) > 0;

                $percentage = 100.0;
                if ($scope !== null && $scope->prefixLength <= $block->prefixLength) {
                    $diffBits = $block->prefixLength - $scope->prefixLength;
                    if ($diffBits < 30) {
                        $percentage = max(2.0, round((1 / (2 ** $diffBits)) * 100, 2));
                    } else {
                        $percentage = 2.0;
                    }
                }

                $partitions[] = [
                    'id' => sprintf('part-%s-%d', $version->value, $idx + 1),
                    'cidr' => $block->normalizedCidr,
                    'raw_input' => $block->rawInput,
                    'version' => $version->value,
                    'type' => $hasCollision ? 'collision' : 'assigned',
                    'status_label' => $hasCollision ? 'Collision' : 'Assigned',
                    'network_ip' => $block->networkIp,
                    'broadcast_ip' => $block->broadcastIp,
                    'prefix_length' => $block->prefixLength,
                    'total_hosts' => $block->totalHosts,
                    'percentage' => $percentage,
                    'conflicting_cidrs' => $conflictingCidrs,
                ];
            }

            if ($freeBlock !== null && $freeBlock->version === $version) {
                $freePercentage = 100.0;
                if ($scope !== null && $scope->prefixLength <= $freeBlock->prefixLength) {
                    $diffBits = $freeBlock->prefixLength - $scope->prefixLength;
                    if ($diffBits < 30) {
                        $freePercentage = max(2.0, round((1 / (2 ** $diffBits)) * 100, 2));
                    } else {
                        $freePercentage = 2.0;
                    }
                }

                $partitions[] = [
                    'id' => sprintf('part-%s-free', $version->value),
                    'cidr' => $freeBlock->normalizedCidr,
                    'raw_input' => $freeBlock->normalizedCidr,
                    'version' => $version->value,
                    'type' => 'recommended_free',
                    'status_label' => 'Recommended Free',
                    'network_ip' => $freeBlock->networkIp,
                    'broadcast_ip' => $freeBlock->broadcastIp,
                    'prefix_length' => $freeBlock->prefixLength,
                    'total_hosts' => $freeBlock->totalHosts,
                    'percentage' => $freePercentage,
                    'conflicting_cidrs' => [],
                ];
            }
        };

        $processGroup($v4Blocks, IpVersion::V4);
        $processGroup($v6Blocks, IpVersion::V6);

        return $partitions;
    }

    /**
     * @param list<CidrBlock> $blocks
     */
    private function findEnclosingSupernet(array $blocks, IpVersion $version): ?CidrBlock
    {
        if (count($blocks) === 0) {
            return null;
        }

        if (count($blocks) === 1) {
            return $blocks[0];
        }

        $minPrefix = $blocks[0]->prefixLength;
        $commonBytes = $blocks[0]->startBytes;

        for ($i = 1; $i < count($blocks); $i++) {
            $otherBytes = $blocks[$i]->startBytes;
            $len = min(strlen($commonBytes), strlen($otherBytes));
            $matchedBits = 0;

            for ($b = 0; $b < $len; $b++) {
                $xor = ord($commonBytes[$b]) ^ ord($otherBytes[$b]);
                if ($xor === 0) {
                    $matchedBits += 8;
                } else {
                    $leadingZeros = 0;
                    for ($bit = 7; $bit >= 0; $bit--) {
                        if (($xor & (1 << $bit)) === 0) {
                            $leadingZeros++;
                        } else {
                            break;
                        }
                    }
                    $matchedBits += $leadingZeros;
                    break;
                }
            }

            $minPrefix = min($minPrefix, $matchedBits, $blocks[$i]->prefixLength);
        }

        $ip = inet_ntop($blocks[0]->startBytes);
        if ($ip === false) {
            return null;
        }

        return CidrBlock::parse(sprintf('%s/%d', $ip, $minPrefix));
    }

    /**
     * @param list<CidrBlock> $blocks
     * @return list<array<string, mixed>>
     */
    private function buildTreeNodes(
        array $blocks,
        ?CidrBlock $parentBlock,
        ?string $freeSubnetCidr
    ): array {
        if (count($blocks) === 0) {
            return [];
        }

        $tree = [];
        $freeBlock = $freeSubnetCidr !== null ? CidrBlock::parse($freeSubnetCidr) : null;

        if ($parentBlock !== null) {
            $children = [];
            foreach ($blocks as $idx => $block) {
                $children[] = [
                    'id' => sprintf('tree-child-%d', $idx + 1),
                    'cidr' => $block->normalizedCidr,
                    'network_ip' => $block->networkIp,
                    'broadcast_ip' => $block->broadcastIp,
                    'prefix_length' => $block->prefixLength,
                    'total_hosts' => $block->totalHosts,
                    'type' => 'assigned',
                    'depth' => 1,
                ];
            }
            if ($freeBlock !== null) {
                $children[] = [
                    'id' => 'tree-child-free',
                    'cidr' => $freeBlock->normalizedCidr,
                    'network_ip' => $freeBlock->networkIp,
                    'broadcast_ip' => $freeBlock->broadcastIp,
                    'prefix_length' => $freeBlock->prefixLength,
                    'total_hosts' => $freeBlock->totalHosts,
                    'type' => 'recommended_free',
                    'depth' => 1,
                ];
            }

            $tree[] = [
                'id' => 'tree-root-parent',
                'cidr' => $parentBlock->normalizedCidr,
                'network_ip' => $parentBlock->networkIp,
                'broadcast_ip' => $parentBlock->broadcastIp,
                'prefix_length' => $parentBlock->prefixLength,
                'total_hosts' => $parentBlock->totalHosts,
                'type' => 'parent',
                'depth' => 0,
                'children' => $children,
            ];

            return $tree;
        }

        $sorted = $blocks;
        usort($sorted, static fn (CidrBlock $a, CidrBlock $b) => strcmp($a->startBytes, $b->startBytes) ?: ($a->prefixLength <=> $b->prefixLength));

        /** @var list<array{block: CidrBlock, children: list<array<string, mixed>>}> $roots */
        $roots = [];
        foreach ($sorted as $idx => $block) {
            $placed = false;
            foreach ($roots as &$root) {
                if ($root['block']->contains($block) && $root['block']->normalizedCidr !== $block->normalizedCidr) {
                    $root['children'][] = [
                        'id' => sprintf('tree-nested-%d', $idx + 1),
                        'cidr' => $block->normalizedCidr,
                        'network_ip' => $block->networkIp,
                        'broadcast_ip' => $block->broadcastIp,
                        'prefix_length' => $block->prefixLength,
                        'total_hosts' => $block->totalHosts,
                        'type' => 'contained',
                        'depth' => 1,
                    ];
                    $placed = true;
                    break;
                }
            }
            unset($root);

            if (!$placed) {
                $roots[] = [
                    'block' => $block,
                    'children' => [],
                ];
            }
        }

        foreach ($roots as $rIdx => $r) {
            $rootBlock = $r['block'];
            $tree[] = [
                'id' => sprintf('tree-root-%d', $rIdx + 1),
                'cidr' => $rootBlock->normalizedCidr,
                'network_ip' => $rootBlock->networkIp,
                'broadcast_ip' => $rootBlock->broadcastIp,
                'prefix_length' => $rootBlock->prefixLength,
                'total_hosts' => $rootBlock->totalHosts,
                'type' => 'assigned',
                'depth' => 0,
                'children' => $r['children'],
            ];
        }

        return $tree;
    }
}
