<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Http;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RetiredToolsController
{
    #[Route('/favicon-suite', name: 'retired_favicon_suite', methods: ['GET', 'POST'])]
    #[Route('/tools/favicon-suite', name: 'retired_legacy_favicon_suite', methods: ['GET'])]
    #[Route('/pl/generator-favicon', name: 'retired_pl_favicon_suite', methods: ['GET'])]
    #[Route('/pl/narzedzia/generator-favicon', name: 'retired_pl_tools_favicon_suite', methods: ['GET'])]
    #[Route('/cors-sandbox', name: 'retired_cors_sandbox', methods: ['GET', 'POST'])]
    #[Route('/tools/cors-sandbox', name: 'retired_legacy_cors_sandbox', methods: ['GET'])]
    #[Route('/pl/piaskownica-cors', name: 'retired_pl_cors_sandbox', methods: ['GET'])]
    #[Route('/pl/narzedzia/piaskownica-cors', name: 'retired_pl_tools_cors_sandbox', methods: ['GET'])]
    #[Route('/bimi-studio', name: 'retired_bimi_studio', methods: ['GET'])]
    #[Route('/tools/bimi-studio', name: 'retired_legacy_bimi_studio', methods: ['GET'])]
    #[Route('/pl/bimi-studio', name: 'retired_pl_bimi_studio', methods: ['GET'])]
    #[Route('/pl/narzedzia/bimi-studio', name: 'retired_pl_tools_bimi_studio', methods: ['GET'])]
    #[Route('/domain-inspector', name: 'retired_domain_inspector', methods: ['GET', 'POST'])]
    #[Route('/tools/domain-inspector', name: 'retired_legacy_domain_inspector', methods: ['GET'])]
    #[Route('/pl/inspektor-domen', name: 'retired_pl_domain_inspector', methods: ['GET'])]
    #[Route('/pl/narzedzia/inspektor-domen', name: 'retired_pl_tools_domain_inspector', methods: ['GET'])]
    #[Route('/caddy-transpiler', name: 'retired_caddy_transpiler', methods: ['GET'])]
    #[Route('/tools/caddy-transpiler', name: 'retired_legacy_caddy_transpiler', methods: ['GET'])]
    #[Route('/pl/konwerter-caddyfile', name: 'retired_pl_caddy_transpiler', methods: ['GET'])]
    #[Route('/pl/narzedzia/konwerter-caddyfile', name: 'retired_pl_tools_caddy_transpiler', methods: ['GET'])]
    #[Route('/cidr-subnet-matrix', name: 'retired_cidr_matrix', methods: ['GET', 'POST'])]
    #[Route('/tools/cidr-subnet-matrix', name: 'retired_legacy_cidr_matrix', methods: ['GET'])]
    #[Route('/pl/matryca-cidr', name: 'retired_pl_cidr_matrix', methods: ['GET'])]
    #[Route('/pl/narzedzia/matryca-cidr', name: 'retired_pl_tools_cidr_matrix', methods: ['GET'])]
    #[Route('/regex-transpiler', name: 'retired_regex_transpiler', methods: ['GET'])]
    #[Route('/tools/regex-transpiler', name: 'retired_legacy_regex_transpiler', methods: ['GET'])]
    #[Route('/pl/konwerter-regex', name: 'retired_pl_regex_transpiler', methods: ['GET'])]
    #[Route('/pl/narzedzia/konwerter-regex', name: 'retired_pl_tools_regex_transpiler', methods: ['GET'])]
    #[Route('/dns-dag-tracer', name: 'retired_dns_dag_tracer', methods: ['GET', 'POST'])]
    #[Route('/tools/dns-dag-tracer', name: 'retired_legacy_dns_dag_tracer', methods: ['GET'])]
    #[Route('/pl/tracer-dns-dag', name: 'retired_pl_dns_dag_tracer', methods: ['GET'])]
    #[Route('/pl/narzedzia/tracer-dns-dag', name: 'retired_pl_tools_dns_dag_tracer', methods: ['GET'])]
    #[Route('/app-links-validator', name: 'retired_app_links_validator', methods: ['GET', 'POST'])]
    #[Route('/tools/app-links-validator', name: 'retired_legacy_app_links_validator', methods: ['GET'])]
    #[Route('/pl/weryfikator-app-links', name: 'retired_pl_app_links_validator', methods: ['GET'])]
    #[Route('/pl/narzedzia/weryfikator-app-links', name: 'retired_pl_tools_app_links_validator', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response('Gone', Response::HTTP_GONE, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
