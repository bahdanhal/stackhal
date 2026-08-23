<?php

declare(strict_types=1);

namespace App\Controller;

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
        $presets = $service->getPresets();

        return $this->render('tools/dns_dag_tracer.html.twig', [
            'domain' => $domain,
            'query_type' => $queryType,
            'result' => $result,
            'presets' => $presets,
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
}
