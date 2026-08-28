<?php

declare(strict_types=1);

namespace App\Toolbox\Presentation\Http;

use App\CidrMatrix\Application\CidrMatrixService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ToolsController extends AbstractController
{
    #[Route(path: ['en' => '/', 'pl' => '/pl/'], name: 'landing', methods: ['GET'])]
    public function landing(): Response
    {
        return $this->render('toolbox/home.html.twig');
    }

    #[Route(path: ['en' => '/tools', 'pl' => '/pl/narzedzia'], name: 'home', methods: ['GET'])]
    public function home(Request $request): Response
    {
        return $this->redirectToRoute('landing', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/ai-studio-local-file-sync', 'pl' => '/pl/synchronizacja-plikow-ai-studio'],
        name: 'ai_studio_extension',
        methods: ['GET']
    )]
    public function aiStudioExtension(): Response
    {
        return $this->render('products/ai_studio_extension.html.twig');
    }

    #[Route(
        path: ['en' => '/bimi-studio', 'pl' => '/pl/bimi-studio'],
        name: 'bimi_studio',
        methods: ['GET']
    )]
    public function bimiStudio(): Response
    {
        return $this->render('tools/bimi.html.twig');
    }

    #[Route(
        path: ['en' => '/tools/bimi-studio', 'pl' => '/pl/narzedzia/bimi-studio'],
        name: 'legacy_bimi_studio',
        methods: ['GET']
    )]
    public function legacyBimiStudio(Request $request): Response
    {
        return $this->redirectToRoute('bimi_studio', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/caddy-transpiler', 'pl' => '/pl/konwerter-caddyfile'],
        name: 'caddy_transpiler',
        methods: ['GET']
    )]
    public function caddyTranspiler(): Response
    {
        return $this->render('tools/caddy_transpiler.html.twig');
    }

    #[Route(
        path: ['en' => '/tools/caddy-transpiler', 'pl' => '/pl/narzedzia/konwerter-caddyfile'],
        name: 'legacy_caddy_transpiler',
        methods: ['GET']
    )]
    public function legacyCaddyTranspiler(Request $request): Response
    {
        return $this->redirectToRoute('caddy_transpiler', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/apple-pkpass-inspector', 'pl' => '/pl/inspektor-pkpass'],
        name: 'pkpass_inspector',
        methods: ['GET']
    )]
    public function pkpassInspector(): Response
    {
        return $this->render('tools/pkpass_inspector.html.twig');
    }

    #[Route(
        path: ['en' => '/tools/apple-pkpass-inspector', 'pl' => '/pl/narzedzia/inspektor-pkpass'],
        name: 'legacy_pkpass_inspector',
        methods: ['GET']
    )]
    public function legacyPkpassInspector(Request $request): Response
    {
        return $this->redirectToRoute('pkpass_inspector', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/cidr-subnet-matrix', 'pl' => '/pl/matryca-cidr'],
        name: 'cidr_matrix',
        methods: ['GET', 'POST']
    )]
    public function cidrMatrix(Request $request, CidrMatrixService $cidrService): Response
    {
        $cidrsInput = (string) $request->request->get('cidrs', $request->query->get('cidrs', '10.0.0.0/16, 10.0.32.0/20'));
        $parentCidr = (string) $request->request->get('parent_cidr', $request->query->get('parent_cidr', ''));
        $requestedFreePrefixString = (string) $request->request->get('requested_free_prefix', $request->query->get('requested_free_prefix', ''));

        $cidrList = array_values(array_filter(array_map('trim', explode(',', str_replace(["\r\n", "\n", "\r"], ',', $cidrsInput)))));
        $requestedFreePrefix = ctype_digit($requestedFreePrefixString) ? (int) $requestedFreePrefixString : null;
        $parentCidrParam = $parentCidr !== '' ? $parentCidr : null;

        $result = $cidrService->analyze($cidrList, $requestedFreePrefix, $parentCidrParam);
        $presets = $cidrService->getPresets();

        return $this->render('tools/cidr_matrix.html.twig', [
            'raw_cidrs' => $cidrsInput,
            'parent_cidr' => $parentCidr,
            'requested_free_prefix' => $requestedFreePrefixString,
            'result' => $result,
            'presets' => $presets,
        ]);
    }

    #[Route(
        path: ['en' => '/tools/cidr-subnet-matrix', 'pl' => '/pl/narzedzia/matryca-cidr'],
        name: 'legacy_cidr_matrix',
        methods: ['GET']
    )]
    public function legacyCidrMatrix(Request $request): Response
    {
        return $this->redirectToRoute('cidr_matrix', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/regex-transpiler', 'pl' => '/pl/konwerter-regex'],
        name: 'regex_transpiler',
        methods: ['GET']
    )]
    public function regexTranspiler(): Response
    {
        return $this->render('tools/regex_transpiler.html.twig');
    }

    #[Route(
        path: ['en' => '/tools/regex-transpiler', 'pl' => '/pl/narzedzia/konwerter-regex'],
        name: 'legacy_regex_transpiler',
        methods: ['GET']
    )]
    public function legacyRegexTranspiler(Request $request): Response
    {
        return $this->redirectToRoute('regex_transpiler', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/favicon-suite', 'pl' => '/pl/generator-favicon'],
        name: 'favicon_suite',
        methods: ['GET', 'POST']
    )]
    public function faviconSuite(Request $request, \App\FaviconSuite\Application\FaviconSuiteService $service): Response
    {
        $presets = $service->getPresets();
        $defaultSvg = $presets[0]['sample_svg'];
        $svgInput = (string) $request->request->get('svg_content', $request->query->get('svg_content', $defaultSvg));
        $strategy = (string) $request->request->get('dark_mode_strategy', $request->query->get('dark_mode_strategy', 'css_invert_fill'));

        $result = $service->generate($svgInput, $strategy);

        return $this->render('tools/favicon_suite.html.twig', [
            'raw_svg' => $svgInput,
            'selected_strategy' => $strategy,
            'result' => $result,
            'presets' => $presets,
        ]);
    }

    #[Route(
        path: ['en' => '/tools/favicon-suite', 'pl' => '/pl/narzedzia/generator-favicon'],
        name: 'legacy_favicon_suite',
        methods: ['GET']
    )]
    public function legacyFaviconSuite(Request $request): Response
    {
        return $this->redirectToRoute('favicon_suite', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/dns-dag-tracer', 'pl' => '/pl/tracer-dns-dag'],
        name: 'dns_dag_tracer',
        methods: ['GET', 'POST']
    )]
    public function dnsDagTracer(Request $request, \App\DnsDagTracer\Application\DnsDagTracerService $service): Response
    {
        $domain = (string) $request->request->get('domain', $request->query->get('domain', 'stackhal.com'));
        $queryType = (string) $request->request->get('query_type', $request->query->get('query_type', 'A'));

        $result = $service->trace($domain, $queryType);
        return $this->render('tools/dns_dag_tracer.html.twig', [
            'domain' => $domain,
            'query_type' => $queryType,
            'result' => $result,
        ]);
    }

    #[Route(
        path: ['en' => '/tools/dns-dag-tracer', 'pl' => '/pl/narzedzia/tracer-dns-dag'],
        name: 'legacy_dns_dag_tracer',
        methods: ['GET']
    )]
    public function legacyDnsDagTracer(Request $request): Response
    {
        return $this->redirectToRoute('dns_dag_tracer', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/app-links-validator', 'pl' => '/pl/weryfikator-app-links'],
        name: 'app_links_validator',
        methods: ['GET', 'POST']
    )]
    public function appLinksValidator(Request $request, \App\AppLinks\Application\AppLinksService $service): Response
    {
        $presets = $service->getPresets();
        $defaultAasa = is_string($encA = json_encode($presets[0]['aasa_content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ? $encA : '{}';
        $defaultAssetLinks = is_string($encL = json_encode($presets[1]['assetlinks_content'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ? $encL : '[]';
        $defaultTestUrl = is_string($presets[0]['test_url'] ?? null) ? (string) $presets[0]['test_url'] : '';

        $testUrl = (string) $request->request->get('test_url', $request->query->get('test_url', $defaultTestUrl));
        $aasaContent = (string) $request->request->get('aasa_content', $request->query->get('aasa_content', $defaultAasa));
        $assetLinksContent = (string) $request->request->get('assetlinks_content', $request->query->get('assetlinks_content', $defaultAssetLinks));

        $result = $service->validate(
            aasa: $aasaContent,
            assetLinks: $assetLinksContent !== '' ? $assetLinksContent : null,
            testUrl: $testUrl !== '' ? $testUrl : null,
        );

        return $this->render('tools/app_links.html.twig', [
            'test_url' => $testUrl,
            'raw_aasa' => $aasaContent,
            'raw_assetlinks' => $assetLinksContent,
            'result' => $result,
            'presets' => $presets,
        ]);
    }

    #[Route(
        path: ['en' => '/tools/app-links-validator', 'pl' => '/pl/narzedzia/weryfikator-app-links'],
        name: 'legacy_app_links_validator',
        methods: ['GET']
    )]
    public function legacyAppLinksValidator(Request $request): Response
    {
        return $this->redirectToRoute('app_links_validator', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route(
        path: ['en' => '/cors-sandbox', 'pl' => '/pl/piaskownica-cors'],
        name: 'cors_sandbox',
        methods: ['GET', 'POST']
    )]
    public function corsSandbox(Request $request, \App\Cors\Application\CorsSandboxService $service): Response
    {
        $presets = $service->getPresets();
        $defaultReqOrigin = is_string($presets[0]['request']['origin'] ?? null) ? (string) $presets[0]['request']['origin'] : '';
        $defaultMethod = is_string($presets[0]['request']['method'] ?? null) ? (string) $presets[0]['request']['method'] : 'GET';
        /** @var array<string, string> $expectedHeaders */
        $expectedHeaders = is_array($presets[0]['expected_response_headers'] ?? null) ? $presets[0]['expected_response_headers'] : [];
        $headerLines = [];
        foreach ($expectedHeaders as $k => $v) {
            $headerLines[] = "{$k}: {$v}";
        }
        $defaultHeaders = implode("\n", $headerLines);

        $reqOrigin = (string) $request->request->get('request_origin', $request->query->get('request_origin', $defaultReqOrigin));
        $reqMethod = (string) $request->request->get('request_method', $request->query->get('request_method', $defaultMethod));
        $reqHeadersRaw = (string) $request->request->get('request_headers', $request->query->get('request_headers', ''));
        $withCredentials = $request->isMethod('POST')
            ? $request->request->getBoolean('with_credentials')
            : $request->query->getBoolean('with_credentials', true);
        $resHeadersRaw = (string) $request->request->get('response_headers', $request->query->get('response_headers', $defaultHeaders));

        $resHeadersList = array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", '', $resHeadersRaw)))));
        $reqHeadersList = array_values(array_filter(array_map('trim', explode(',', $reqHeadersRaw))));

        $result = $service->analyze(
            requestOrigin: $reqOrigin,
            responseHeaders: $resHeadersList,
            withCredentials: $withCredentials,
            requestMethod: $reqMethod,
            requestHeaders: $reqHeadersList,
        );

        return $this->render('tools/cors_sandbox.html.twig', [
            'request_origin' => $reqOrigin,
            'request_method' => $reqMethod,
            'request_headers_raw' => $reqHeadersRaw,
            'with_credentials' => $withCredentials,
            'response_headers_raw' => $resHeadersRaw,
            'result' => $result,
            'presets' => $presets,
        ]);
    }

    #[Route(
        path: ['en' => '/tools/cors-sandbox', 'pl' => '/pl/narzedzia/piaskownica-cors'],
        name: 'legacy_cors_sandbox',
        methods: ['GET']
    )]
    public function legacyCorsSandbox(Request $request): Response
    {
        return $this->redirectToRoute('cors_sandbox', ['_locale' => $request->getLocale()], Response::HTTP_MOVED_PERMANENTLY);
    }
}
