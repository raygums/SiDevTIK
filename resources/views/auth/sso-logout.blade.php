{{--
    SSO Relay Logout Page
    =====================
    This page serves as a bridge between our local logout and the IdP logout.

    Why this approach:
    - redirect()->away(ssoLogoutUrl) takes the user to the IdP's domain.
      The IdP then redirects to ITS OWN login page (akses.unila.ac.id/auth/login),
      leaving the user stranded with no path back to our application.

    - This page keeps the user on OUR domain and loads the IdP logout URL
      in a hidden <iframe>. The browser sends the user's SSO cookies to the
      IdP, which destroys the server-side session and clears the SSO cookie.

    - After a 2-second delay, JavaScript redirects the user to our login page.

    Fallback behavior:
    - If the IdP sets X-Frame-Options: DENY, the iframe is blocked silently.
      The user still lands on our login page. The `prompt=login` parameter
      in redirectToSSO() acts as the secondary safeguard in this case.

    - If JavaScript is disabled, the "Lanjut ke halaman login" link provides
      manual navigation.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keluar - SiDevTIK</title>
    <link rel="icon" href="{{ asset('images/logo-unila.png') }}" type="image/png">

    {{-- Preload the login page so the redirect feels instant --}}
    <link rel="prefetch" href="{{ $loginUrl }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 50%, #f8fafc 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #374151;
        }

        .card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 420px;
            width: 90%;
        }

        .icon-wrap {
            width: 4rem;
            height: 4rem;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .icon-wrap svg {
            width: 2rem;
            height: 2rem;
            color: #16a34a;
        }

        h1 {
            font-size: 1.375rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #1d4ed8, #3b82f6);
            border-radius: 9999px;
            transition: width 2s linear;
        }

        .redirect-text {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-block;
            padding: 0.625rem 1.5rem;
            background: #1d4ed8;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 0.625rem;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn:hover { background: #1e40af; }

        /* Hidden iframe — triggers IdP logout without navigating away */
        .idp-logout-frame {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
            border: none;
        }
    </style>
</head>
<body>

    {{--
        Hidden iframe — the browser loads the IdP logout URL with the user's
        SSO cookies, destroying the server-side SSO session in the background.
        The user never navigates away from our domain.
    --}}
    <iframe
        class="idp-logout-frame"
        src="{{ $ssoLogoutUrl }}"
        aria-hidden="true"
        title="SSO Logout"
    ></iframe>

    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1>Berhasil Keluar</h1>
        <p class="subtitle">
            Anda telah keluar dari akun SSO Unila.<br>
            Sesi Anda telah dihapus dengan aman.
        </p>

        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>

        <p class="redirect-text" id="redirectText">
            Mengalihkan ke halaman login...
        </p>

        <a href="{{ $loginUrl }}" class="btn">
            Lanjut ke Halaman Login
        </a>
    </div>

    <script>
        (function () {
            var loginUrl    = {{ Js::from($loginUrl) }};
            var progressFill = document.getElementById('progressFill');
            var redirectText = document.getElementById('redirectText');
            var delay       = 2000; // 2 seconds

            // Animate the progress bar over the delay duration
            requestAnimationFrame(function () {
                progressFill.style.width = '100%';
            });

            // Countdown display
            var remaining = Math.ceil(delay / 1000);
            var interval  = setInterval(function () {
                remaining -= 1;
                if (remaining > 0) {
                    redirectText.textContent = 'Mengalihkan ke halaman login dalam ' + remaining + ' detik...';
                }
            }, 1000);

            // Redirect after delay
            setTimeout(function () {
                clearInterval(interval);
                window.location.href = loginUrl;
            }, delay);
        }());
    </script>

</body>
</html>
