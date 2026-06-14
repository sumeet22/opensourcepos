<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\DatabaseHandler;

class App extends BaseConfig
{
    /**
     * This is the code version of the Open Source Point of Sale you're running.
     *
     * @var string
     */
    public string $application_version = '3.4.2';

    /**
     * This is the commit hash for the version you are currently using.
     *
     * @var string
     */
    public string $commit_sha1 = 'dev';

    /**
     * Logs are stored in writable/logs
     *
     * @var bool
     */
    public bool $db_log_enabled = false;

    /**
     * DB Query Log only long-running queries
     *
     * @var bool
     */
    public bool $db_log_only_long = false;

    /**
     * Defines whether to require/reroute to HTTPS
     *
     * @var bool
     */
    public bool $https_on;    // Set in the constructor

    /**
     * --------------------------------------------------------------------------
     * Base Site URL
     * --------------------------------------------------------------------------
     *
     * URL to your CodeIgniter root. Typically, this will be your base URL,
     * WITH a trailing slash:
     *
     * E.g., http://example.com/
     */
    public string $baseURL;    // Defined in the constructor

    /**
     * Allowed Hostnames in the Site URL other than the hostname in the baseURL.
     * If you want to accept multiple Hostnames, set this.
     *
     * Or via environment variable (useful for Docker/Compose):
     *   ALLOWED_HOSTNAMES=example.com,www.example.com
     *
     *     ['media.example.com', 'accounts.example.com']
     *
     * @var list<string>
     */
    public array $allowedHostnames = [];

    /**
     * --------------------------------------------------------------------------
     * Index File
     * --------------------------------------------------------------------------
     *
     * Typically, this will be your `index.php` file, unless you've renamed it to
     * something else. If you have configured your web server to remove this file
     * from your site URIs, set this variable to an empty string.
     */
    public string $indexPage = '';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     *
     * This item determines which server global should be used to retrieve the
     * URI string. The default setting of 'REQUEST_URI' works for most servers.
     * If your links do not seem to work, try one of the other delicious flavors:
     *
     *  'REQUEST_URI': Uses $_SERVER['REQUEST_URI']
     * 'QUERY_STRING': Uses $_SERVER['QUERY_STRING']
     *    'PATH_INFO': Uses $_SERVER['PATH_INFO']
     *
     * WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
     */
    public string $uriProtocol = 'REQUEST_URI';

    /*
    |--------------------------------------------------------------------------
    | Allowed URL Characters
    |--------------------------------------------------------------------------
    |
    | This lets you specify which characters are permitted within your URLs.
    | When someone tries to submit a URL with disallowed characters they will
    | get a warning message.
    |
    | As a security measure you are STRONGLY encouraged to restrict URLs to
    | as few characters as possible.
    |
    | By default, only these are allowed: `a-z 0-9~%.:_-`
    |
    | Set an empty string to allow all characters -- but only if you are insane.
    |
    | The configured value is actually a regular expression character group
    | and it will be used as: '/\A[<permittedURIChars>]+\z/iu'
    |
    | DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
    |
    */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    /**
     * --------------------------------------------------------------------------
     * Default Locale
     * --------------------------------------------------------------------------
     *
     * The Locale roughly represents the language and location that your visitor
     * is viewing the site from. It affects the language strings and other
     * strings (like currency markers, numbers, etc), that your program
     * should run under for this request.
     */
    public string $defaultLocale = 'en';

    /**
     * --------------------------------------------------------------------------
     * Negotiate Locale
     * --------------------------------------------------------------------------
     *
     * If true, the current Request object will automatically determine the
     * language to use based on the value of the Accept-Language header.
     *
     * If false, no automatic detection will be performed.
     */
    public bool $negotiateLocale = true;

    /**
     * --------------------------------------------------------------------------
     * Supported Locales
     * --------------------------------------------------------------------------
     *
     * If $negotiateLocale is true, this array lists the locales supported
     * by the application in descending order of priority. If no match is
     * found, the first locale will be used.
     *
     * IncomingRequest::setLocale() also uses this list.
     *
     * @var list<string>
     */
    public array $supportedLocales = [
        'ar-EG',
        'ar-LB',
        'az',
        'bg',
        'bs',
        'ckb',
        'cs',
        'da',
        'de-CH',
        'de-DE',
        'el',
        'en',
        'en-GB',
        'es-ES',
        'es-MX',
        'fa',
        'fr',
        'he',
        'hr-HR',
        'hu',
        'hy',
        'id',
        'it',
        'km',
        'lo',
        'ml',
        'nb',
        'nl-BE',
        'nl-NL',
        'pl',
        'pt-BR',
        'ro',
        'ru',
        'sv',
        'ta',
        'th',
        'tl',
        'tr',
        'uk',
        'ur',
        'vi',
        'zh-Hans',
        'zh-Hant',
    ];

    /**
     * --------------------------------------------------------------------------
     * Application Timezone
     * --------------------------------------------------------------------------
     *
     * The default timezone that will be used in your application to display
     * dates with the date helper, and can be retrieved through app_timezone()
     *
     * @see https://www.php.net/manual/en/timezones.php for list of timezones
     *      supported by PHP.
     */
    public string $appTimezone = 'UTC';

    /**
     * --------------------------------------------------------------------------
     * Default Character Set
     * --------------------------------------------------------------------------
     *
     * This determines which character set is used by default in various methods
     * that require a character set to be provided.
     *
     * @see http://php.net/htmlspecialchars for a list of supported charsets.
     */
    public string $charset = 'UTF-8';

    /**
     * --------------------------------------------------------------------------
     * Force Global Secure Requests
     * --------------------------------------------------------------------------
     *
     * If true, this will force every request made to this application to be
     * made via a secure connection (HTTPS). If the incoming request is not
     * secure, the user will be redirected to a secure version of the page
     * and the HTTP Strict Transport Security (HSTS) header will be set.
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * --------------------------------------------------------------------------
     * Reverse Proxy IPs
     * --------------------------------------------------------------------------
     *
     * If your server is behind a reverse proxy, you must whitelist the proxy
     * IP addresses from which CodeIgniter should trust headers such as
     * X-Forwarded-For or Client-IP in order to properly identify
     * the visitor's IP address.
     *
     * You need to set a proxy IP address or IP address with subnets and
     * the HTTP header for the client IP address.
     *
     * Here are some examples:
     *     [
     *         '10.0.1.200'     => 'X-Forwarded-For',
     *         '192.168.5.0/24' => 'X-Real-IP',
     *     ]
     *
     * @var array<string, string>
     */
    public array $proxyIPs = [];

    /**
     * --------------------------------------------------------------------------
     * Content Security Policy
     * --------------------------------------------------------------------------
     *
     * Enables the Response's Content Secure Policy to restrict the sources that
     * can be used for images, scripts, CSS files, audio, video, etc. If enabled,
     * the Response object will populate default values for the policy from the
     * `ContentSecurityPolicy.php` file. Controllers can always add to those
     * restrictions at run time.
     *
     * For a better understanding of CSP, see these documents:
     *
     * @see http://www.html5rocks.com/en/tutorials/security/content-security-policy/
     * @see http://www.w3.org/TR/CSP/
     */
    public bool $CSPEnabled = false;

    public function __construct()
    {
        parent::__construct();

        // Solution for CodeIgniter 4 limitation: arrays cannot be set from .env
        // See: https://github.com/codeigniter4/CodeIgniter4/issues/7311
        $envAllowedHostnames = $this->getEnvString('ALLOWED_HOSTNAMES')
            ?? $this->getEnvString('app.allowedHostnames');

        if ($envAllowedHostnames !== null) {
            $this->allowedHostnames = array_values(array_filter(
                array_map('trim', explode(',', $envAllowedHostnames)),
                static fn (string $hostname): bool => $hostname !== ''
            ));
        }

        $configuredBaseUrl = $this->getConfiguredBaseUrl();

        if ($configuredBaseUrl !== null) {
            $this->https_on = str_starts_with(strtolower($configuredBaseUrl), 'https://');
            $this->baseURL = $configuredBaseUrl;

            return;
        }

        $this->https_on = $this->isHttpsRequest();

        $host = $this->getValidHost();
        $this->baseURL = $this->buildBaseUrl($host);
    }

    private function getConfiguredBaseUrl(): ?string
    {
        $baseUrl = $this->getEnvString('APP_BASE_URL') ?? $this->getEnvString('app.baseURL');

        if ($baseUrl === null) {
            return null;
        }

        $baseUrl = trim($baseUrl, " \t\n\r\0\x0B\"'");

        if ($baseUrl === '') {
            return null;
        }

        return str_ends_with($baseUrl, '/') ? $baseUrl : $baseUrl . '/';
    }

    private function isHttpsRequest(): bool
    {
        $forceHttps = $this->getEnvString('FORCE_HTTPS');

        $forceHttps = strtolower(trim($forceHttps));

        if (in_array($forceHttps, ['1', 'true', 'on', 'yes'], true)) {
            return true;
        }

        if (isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) === 'on') {
            return true;
        }

        if (isset($_SERVER['REQUEST_SCHEME']) && strtolower((string) $_SERVER['REQUEST_SCHEME']) === 'https') {
            return true;
        }

        $forwardedProto = $this->getForwardedValue('HTTP_X_FORWARDED_PROTO', 'X_FORWARDED_PROTO');

        if ($forwardedProto !== null) {
            $forwardedProto = strtolower(trim(explode(',', $forwardedProto)[0]));

            return $forwardedProto === 'https';
        }

        return false;
    }

    private function buildBaseUrl(string $host): string
    {
        $scheme = $this->https_on ? 'https' : 'http';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

        return $scheme . '://' . $host . '/' . str_replace(basename($scriptName), '', $scriptName);
    }

    /**
     * Validates and returns a trusted hostname.
     *
     * Security: Prevents Host Header Injection attacks (GHSA-jchf-7hr6-h4f3)
     * by validating the HTTP_HOST against a whitelist of allowed hostnames.
     *
     * In production: Fails fast if allowedHostnames is not configured.
     * In development: Allows localhost fallback with an error log.
     *
     * @return string A validated hostname
     * @throws \RuntimeException If allowedHostnames is not configured in production
     */
    private function getValidHost(): string
    {
        $httpHost = $this->getRequestHost();

        // Determine environment
        // CodeIgniter's test bootstrap sets $_SERVER['CI_ENVIRONMENT'] = 'testing'
        // Check $_SERVER first, then $_ENV, then fall back to 'production'
        $environment = $_SERVER['CI_ENVIRONMENT'] ?? $_ENV['CI_ENVIRONMENT'] ?? getenv('CI_ENVIRONMENT') ?: 'production';

        if (empty($this->allowedHostnames)) {
            $errorMessage =
                'Security: allowedHostnames is not configured. ' .
                'Host header injection protection is disabled. ' .
                'Set app.allowedHostnames in your .env file or ALLOWED_HOSTNAMES environment variable. ' .
                'Example: app.allowedHostnames = "example.com,www.example.com" ' .
                'Received Host: ' . $httpHost;

            // Production: Fail explicitly to prevent silent security vulnerabilities
            // Testing and development: Allow localhost fallback
            if ($environment === 'production') {
                throw new \RuntimeException($errorMessage);
            }

            log_message('error', $errorMessage . ' Using localhost fallback (development only).');
            return 'localhost';
        }

        $allowedHostnames = array_map([$this, 'normalizeHostname'], $this->allowedHostnames);

        if (in_array($this->normalizeHostname($httpHost), $allowedHostnames, true)) {
            return $httpHost;
        }

        // Host not in whitelist - use first configured hostname as fallback
        log_message('warning',
            'Security: Rejected HTTP_HOST "' . $httpHost . '" - not in allowedHostnames whitelist. ' .
            'Using fallback: ' . $this->allowedHostnames[0]
        );

        return $this->allowedHostnames[0];
    }

    private function getRequestHost(): string
    {
        $forwardedHost = $this->getForwardedValue('HTTP_X_FORWARDED_HOST', 'X_FORWARDED_HOST');

        if ($forwardedHost !== null) {
            $host = $this->parseForwardedHost($forwardedHost);

            if ($host !== '') {
                return $host;
            }
        }

        return $_SERVER['HTTP_HOST'] ?? 'localhost';
    }

    private function getForwardedValue(string $serverKey, string $envKey): ?string
    {
        $value = $_SERVER[$serverKey] ?? null;

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        $envValue = $this->getEnvString($envKey);

        if ($envValue !== null) {
            return $envValue;
        }

        return null;
    }

    private function parseForwardedHost(string $forwardedHost): string
    {
        $host = trim(explode(',', $forwardedHost)[0]);

        if (preg_match('#^https?://#i', $host) === 1) {
            $parsedHost = parse_url($host, PHP_URL_HOST);

            if (is_string($parsedHost) && $parsedHost !== '') {
                return $parsedHost;
            }
        }

        return trim($host, " \t\n\r\0\x0B\"'");
    }

    private function normalizeHostname(string $hostname): string
    {
        $hostname = strtolower(trim($hostname));
        $hostname = rtrim($hostname, '.');

        if (str_starts_with($hostname, '[')) {
            if (preg_match('/^\[([^\]]+)\]/', $hostname, $matches) === 1) {
                return strtolower($matches[1]);
            }
        }

        if (substr_count($hostname, ':') === 1) {
            $hostname = explode(':', $hostname, 2)[0];
        }

        return $hostname;
    }

    private function getEnvString(string $key): ?string
    {
        $value = env($key);

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        $raw = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if (is_string($raw) && trim($raw) !== '') {
            return $raw;
        }

        return null;
    }
}
