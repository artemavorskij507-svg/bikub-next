<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>{{ __('bikube.public.home.page_title') }}</title>
        <meta name="description" content="{{ __('bikube.public.home.page_description') }}" />
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                color-scheme: dark;
                font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                background: #040a14;
                color: #f8fafc;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                background:
                    radial-gradient(85% 70% at 50% 0%, rgba(16, 47, 101, 0.4) 0%, rgba(4, 10, 30, 0) 65%),
                    linear-gradient(180deg, #050d1a 0%, #03070f 100%);
                min-height: 100vh;
                overflow-x: hidden;
                -webkit-font-smoothing: antialiased;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .page__content {
                flex: 1;
            }

            /* Glassmorphism Header */
            .site-header {
                position: sticky;
                top: 0;
                z-index: 100;
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                background: rgba(4, 11, 28, 0.85);
                border-bottom: 1px solid rgba(161, 196, 255, 0.08);
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            }

            .site-header__inner {
                max-width: 1200px;
                margin: 0 auto;
                padding: 16px 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-weight: 900;
                font-size: 1.5rem;
                letter-spacing: -0.02em;
                color: #ffffff;
            }

            .brand__mark {
                width: 32px;
                height: 32px;
                border-radius: 999px;
                background: linear-gradient(135deg, #b8f829, #76b90e);
                box-shadow: 0 0 15px rgba(184, 248, 41, 0.4);
                position: relative;
                display: inline-block;
            }

            .brand__mark::after {
                content: "";
                position: absolute;
                inset: 7px;
                border-radius: 999px;
                background: #041022;
            }

            .nav-links {
                display: flex;
                gap: 24px;
                align-items: center;
            }

            .nav-links a {
                color: #cde2ff;
                font-size: 0.9rem;
                font-weight: 500;
                transition: color 0.2s ease;
            }

            .nav-links a:hover {
                color: #b8f829;
            }

            .header-actions {
                display: flex;
                gap: 12px;
                align-items: center;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                padding: 10px 20px;
                font-weight: 700;
                transition: all 0.2s ease;
                font-size: 0.9rem;
                border: 1px solid transparent;
                cursor: pointer;
            }

            .button--primary {
                background: linear-gradient(135deg, #b8f829 0%, #76b90e 100%);
                color: #041022;
                box-shadow: 0 4px 15px rgba(184, 248, 41, 0.25);
            }

            .button--primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(184, 248, 41, 0.4);
                filter: brightness(1.05);
            }

            .button--secondary {
                border-color: rgba(255, 255, 255, 0.15);
                color: #f3f8ff;
                background: rgba(10, 23, 47, 0.45);
            }

            .button--secondary:hover {
                border-color: rgba(184, 248, 41, 0.4);
                background: rgba(184, 248, 41, 0.05);
                color: #b8f829;
            }

            /* Hero Section */
            .hero {
                position: relative;
                overflow: hidden;
                padding: 120px 24px 80px;
                background: linear-gradient(180deg, rgba(16, 47, 101, 0.15) 0%, transparent 60%);
            }

            .hero__inner {
                max-width: 1200px;
                margin: 0 auto;
                display: grid;
                gap: 48px;
                grid-template-columns: 1.1fr 0.9fr;
                align-items: center;
            }

            .hero-copy {
                position: relative;
                z-index: 10;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 999px;
                background: rgba(8, 23, 48, 0.8);
                border: 1px solid rgba(169, 203, 247, 0.25);
                color: #def0ff;
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                margin-bottom: 24px;
                font-weight: 700;
            }

            .badge__dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: #b8f829;
                box-shadow: 0 0 10px rgba(184, 248, 41, 0.8);
            }

            .badge__flag {
                display: inline-flex;
                min-width: 20px;
                height: 14px;
                border-radius: 2px;
                background: linear-gradient(90deg, #ba1c2b 0 28%, #fff 28% 35%, #00205b 35% 65%, #fff 65% 72%, #ba1c2b 72%);
            }

            .hero-copy h1 {
                margin: 0 0 20px;
                font-size: clamp(2.5rem, 5vw, 4rem);
                line-height: 1.1;
                color: #ffffff;
                font-weight: 850;
                letter-spacing: -0.02em;
            }

            .hero-copy h1 span {
                color: #b8f829;
                background: linear-gradient(to right, #b8f829, #8ae610);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .hero-copy p {
                margin: 0 0 36px;
                color: #b9d2ee;
                font-size: 1.1rem;
                line-height: 1.6;
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                margin-bottom: 48px;
            }

            .hero-stats {
                display: grid;
                gap: 16px;
                grid-template-columns: repeat(2, 1fr);
                max-width: 500px;
            }

            .stat-card {
                background: rgba(8, 24, 49, 0.5);
                border: 1px solid rgba(160, 195, 243, 0.15);
                border-radius: 16px;
                padding: 18px;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                transition: border-color 0.2s ease;
            }

            .stat-card:hover {
                border-color: rgba(184, 248, 41, 0.3);
            }

            .stat-card__value {
                font-size: 1.8rem;
                font-weight: 850;
                color: #b8f829;
                margin-bottom: 4px;
            }

            .stat-card__label {
                color: #c3d8f4;
                font-size: 0.85rem;
                font-weight: 500;
            }

            /* Hero Device/Art Panel */
            .hero-art {
                position: relative;
                z-index: 10;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .hero-art__glow {
                position: absolute;
                width: 120%;
                height: 120%;
                background: radial-gradient(circle, rgba(16, 47, 101, 0.4) 0%, transparent 70%);
                filter: blur(40px);
                pointer-events: none;
            }

            .hero-art__wrapper {
                position: relative;
                width: 100%;
                max-width: 480px;
                border-radius: 24px;
                border: 1px solid rgba(161, 196, 255, 0.15);
                background: rgba(4, 11, 28, 0.6);
                padding: 12px;
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                overflow: hidden;
            }

            .hero-art__image {
                width: 100%;
                height: auto;
                border-radius: 16px;
                display: block;
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .hero-art__badge {
                position: absolute;
                bottom: 24px;
                left: 24px;
                background: rgba(4, 11, 28, 0.95);
                border: 1px solid rgba(184, 248, 41, 0.3);
                border-radius: 16px;
                padding: 16px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(10px);
                animation: float-badge 6s ease-in-out infinite;
            }

            @keyframes float-badge {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-8px); }
            }

            /* Sections layout */
            .section {
                max-width: 1200px;
                margin: 0 auto;
                padding: 80px 24px;
            }

            .section__head {
                margin-bottom: 48px;
                max-width: 800px;
            }

            .section__eyebrow {
                color: #b8f829;
                text-transform: uppercase;
                letter-spacing: 0.15em;
                font-size: 0.8rem;
                font-weight: 800;
                margin-bottom: 12px;
            }

            .section__title {
                font-size: clamp(2rem, 3.5vw, 3rem);
                color: #ffffff;
                font-weight: 850;
                letter-spacing: -0.02em;
                line-height: 1.15;
            }

            .section__text {
                margin-top: 16px;
                color: #a8c3e2;
                font-size: 1.05rem;
                line-height: 1.5;
            }

            /* Categories Grid */
            .categories-grid {
                display: grid;
                gap: 20px;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }

            .category-card {
                position: relative;
                border-radius: 20px;
                min-height: 200px;
                padding: 24px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                border: 1px solid rgba(159, 194, 241, 0.15);
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            }

            .category-card::before {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(4, 16, 36, 0.1) 0%, rgba(4, 16, 36, 0.85) 100%);
                z-index: 1;
                transition: opacity 0.3s ease;
            }

            .category-card:hover {
                transform: translateY(-6px);
                border-color: rgba(184, 248, 41, 0.5);
                box-shadow: 0 20px 40px rgba(184, 248, 41, 0.15);
            }

            .category-card:hover::before {
                background: linear-gradient(180deg, rgba(4, 16, 36, 0.2) 0%, rgba(4, 16, 36, 0.92) 100%);
            }

            .category-card__header {
                position: relative;
                z-index: 2;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .category-card__num {
                font-size: 0.85rem;
                font-weight: 800;
                color: #b8f829;
                background: rgba(4, 10, 24, 0.7);
                padding: 4px 8px;
                border-radius: 6px;
                border: 1px solid rgba(184, 248, 41, 0.2);
            }

            .category-card__content {
                position: relative;
                z-index: 2;
                margin-top: auto;
            }

            .category-card__title {
                font-size: 1.5rem;
                font-weight: 800;
                color: #ffffff;
                margin-bottom: 8px;
                letter-spacing: -0.01em;
            }

            .category-card__desc {
                color: #cbd5e1;
                font-size: 0.9rem;
                line-height: 1.4;
                margin-bottom: 16px;
            }

            .category-card__arrow {
                width: 36px;
                height: 36px;
                border-radius: 999px;
                background: rgba(4, 10, 24, 0.8);
                color: #b8f829;
                border: 1px solid rgba(184, 248, 41, 0.3);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                transition: all 0.2s ease;
            }

            .category-card:hover .category-card__arrow {
                background: #b8f829;
                color: #041022;
                border-color: #b8f829;
                transform: translateX(4px);
            }

            /* Dropdown lists of Scenarios inside Categories */
            .category-scenarios {
                margin-top: 14px;
                background: rgba(4, 11, 28, 0.6);
                border: 1px solid rgba(161, 196, 255, 0.1);
                border-radius: 12px;
                padding: 8px;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .category-scenarios__item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 12px;
                border-radius: 8px;
                background: transparent;
                transition: all 0.2s ease;
                font-size: 0.85rem;
                font-weight: 600;
                color: #cbd5e1;
            }

            .category-scenarios__item:hover {
                background: rgba(184, 248, 41, 0.08);
                color: #b8f829;
                padding-left: 16px;
            }

            .category-scenarios__arrow {
                font-size: 0.75rem;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            .category-scenarios__item:hover .category-scenarios__arrow {
                opacity: 1;
            }

            /* Popular Scenarios Horizontal Slider */
            .popular-scenarios {
                background: rgba(7, 20, 45, 0.4);
                border-radius: 24px;
                border: 1px solid rgba(130, 168, 225, 0.15);
            }

            .bk-horizontal {
                display: flex;
                gap: 16px;
                overflow-x: auto;
                scroll-behavior: smooth;
                padding: 4px 0 16px;
                scrollbar-width: thin;
                scrollbar-color: rgba(135, 170, 214, 0.3) transparent;
            }

            .bk-horizontal::-webkit-scrollbar {
                height: 8px;
            }

            .bk-horizontal::-webkit-scrollbar-thumb {
                background: rgba(135, 170, 214, 0.3);
                border-radius: 999px;
            }

            .scenario-card {
                min-width: 260px;
                flex: 0 0 auto;
                min-height: 180px;
                border-radius: 16px;
                border: 1px solid rgba(156, 192, 245, 0.15);
                padding: 20px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                color: #ffffff;
                background-size: cover;
                background-position: center;
                position: relative;
                overflow: hidden;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
                transition: all 0.2s ease;
            }

            .scenario-card::before {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(166deg, rgba(4, 16, 36, 0.2), rgba(4, 16, 36, 0.85));
                z-index: 1;
            }

            .scenario-card:hover {
                transform: translateY(-4px);
                border-color: rgba(184, 248, 41, 0.3);
            }

            .scenario-card__title {
                position: relative;
                z-index: 2;
                font-size: 1.25rem;
                line-height: 1.2;
                font-weight: 800;
                max-width: 90%;
            }

            .scenario-card__footer {
                position: relative;
                z-index: 2;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .scenario-card__action {
                border-radius: 999px;
                padding: 6px 14px;
                background: rgba(184, 248, 41, 0.12);
                border: 1px solid rgba(184, 248, 41, 0.4);
                color: #b8f829;
                font-size: 0.75rem;
                font-weight: 700;
            }

            /* Process Steps */
            .steps-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 24px;
                margin-top: 16px;
            }

            .step-item {
                position: relative;
                background: linear-gradient(180deg, rgba(7, 21, 47, 0.4), rgba(4, 14, 33, 0.5));
                border: 1px solid rgba(146, 184, 236, 0.15);
                border-radius: 20px;
                padding: 32px 24px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                transition: border-color 0.2s ease;
            }

            .step-item:hover {
                border-color: rgba(184, 248, 41, 0.3);
            }

            .step-item:not(:last-child)::after {
                content: "→";
                position: absolute;
                top: 50%;
                right: -18px;
                font-size: 1.5rem;
                color: rgba(184, 248, 41, 0.5);
                transform: translateY(-50%);
                z-index: 10;
            }

            .step-icon {
                width: 64px;
                height: 64px;
                border-radius: 999px;
                background: rgba(184, 248, 41, 0.1);
                border: 1px solid rgba(184, 248, 41, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                margin-bottom: 20px;
                box-shadow: 0 0 20px rgba(184, 248, 41, 0.08);
            }

            .step-item h3 {
                font-size: 1.15rem;
                font-weight: 800;
                color: #ffffff;
                margin-bottom: 10px;
            }

            .step-item p {
                color: #cbd5e1;
                font-size: 0.9rem;
                line-height: 1.4;
            }

            /* Banners Split Grid */
            .split-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .banner-card {
                position: relative;
                border-radius: 24px;
                padding: 40px;
                min-height: 240px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                border: 1px solid rgba(130, 168, 225, 0.15);
                background-size: cover;
                background-position: center;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                transition: transform 0.2s ease, border-color 0.2s ease;
            }

            .banner-card::before {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(4, 16, 36, 0.65) 0%, rgba(4, 16, 36, 0.9) 100%);
                z-index: 1;
            }

            .banner-card:hover {
                transform: translateY(-4px);
                border-color: rgba(184, 248, 41, 0.3);
            }

            .banner-card__content {
                position: relative;
                z-index: 2;
                max-width: 80%;
            }

            .banner-card__title {
                font-size: 1.75rem;
                font-weight: 850;
                color: #ffffff;
                margin-bottom: 10px;
                letter-spacing: -0.02em;
            }

            .banner-card__text {
                color: #cbd5e1;
                font-size: 0.95rem;
                line-height: 1.5;
            }

            .banner-card__link {
                position: relative;
                z-index: 2;
                align-self: flex-start;
                font-size: 0.9rem;
                font-weight: 700;
                color: #b8f829;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .banner-card__link::after {
                content: "→";
                transition: transform 0.2s ease;
            }

            .banner-card:hover .banner-card__link::after {
                transform: translateX(4px);
            }

            /* Mobile App Promo Block */
            .promo-block {
                display: grid;
                grid-template-columns: 1.1fr 0.9fr;
                gap: 48px;
                align-items: center;
                background: linear-gradient(135deg, rgba(8, 24, 49, 0.6) 0%, rgba(4, 11, 28, 0.8) 100%);
                border: 1px solid rgba(130, 168, 225, 0.15);
                border-radius: 28px;
                padding: 48px;
                overflow: hidden;
            }

            .promo-block__left h3 {
                font-size: 2.2rem;
                font-weight: 850;
                color: #ffffff;
                margin-bottom: 16px;
                letter-spacing: -0.02em;
            }

            .promo-block__left p {
                color: #cbd5e1;
                font-size: 1.05rem;
                line-height: 1.6;
                margin-bottom: 32px;
            }

            .store-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                align-items: center;
            }

            .store-button {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 12px 24px;
                border-radius: 12px;
                background: #030812;
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: #ffffff;
                font-weight: 700;
                font-size: 0.85rem;
                transition: all 0.2s ease;
            }

            .store-button:hover {
                border-color: rgba(184, 248, 41, 0.4);
                background: rgba(184, 248, 41, 0.04);
            }

            .qr-code {
                display: flex;
                align-items: center;
                gap: 16px;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 16px;
                padding: 12px 18px;
            }

            .qr-code__box {
                width: 60px;
                height: 60px;
                background: #ffffff;
                border-radius: 8px;
                display: grid;
                place-items: center;
                color: #030812;
                font-weight: 900;
                font-size: 0.75rem;
            }

            .qr-code__text {
                font-size: 0.8rem;
                color: #cbd5e1;
                line-height: 1.3;
            }

            .promo-block__right {
                position: relative;
                display: flex;
                gap: 16px;
                justify-content: center;
                height: 340px;
            }

            .promo-card {
                position: absolute;
                border-radius: 16px;
                background-size: cover;
                background-position: center;
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
                transition: all 0.3s ease;
            }

            .promo-card--phone {
                width: 170px;
                height: 320px;
                z-index: 10;
                left: 10%;
                top: 0;
                animation: promo-float-1 7s ease-in-out infinite;
            }

            .promo-card--panel {
                width: 190px;
                height: 270px;
                z-index: 5;
                right: 5%;
                top: 30px;
                animation: promo-float-2 8s ease-in-out infinite;
            }

            @keyframes promo-float-1 {
                0%, 100% { transform: translateY(0) rotate(2deg); }
                50% { transform: translateY(-10px) rotate(0deg); }
            }

            @keyframes promo-float-2 {
                0%, 100% { transform: translateY(0) rotate(-2deg); }
                50% { transform: translateY(-8px) rotate(1deg); }
            }

            /* Footer */
            .footer {
                background: #03070f;
                border-top: 1px solid rgba(161, 196, 255, 0.08);
                padding: 64px 24px 32px;
                color: #8b98a8;
            }

            .footer__inner {
                max-width: 1200px;
                margin: 0 auto;
            }

            .footer-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 40px;
                margin-bottom: 48px;
            }

            .footer-col h4 {
                color: #ffffff;
                font-weight: 800;
                font-size: 1rem;
                margin-bottom: 20px;
            }

            .footer-links {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .footer-links a {
                color: #8b98a8;
                font-size: 0.9rem;
                transition: color 0.2s ease;
            }

            .footer-links a:hover {
                color: #b8f829;
            }

            .footer-contact {
                display: flex;
                flex-direction: column;
                gap: 12px;
                font-size: 0.9rem;
            }

            .footer-contact__item {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .footer-contact__icon {
                color: #b8f829;
            }

            .footer-bottom {
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                padding-top: 32px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 16px;
                font-size: 0.8rem;
            }

            .footer-legal {
                display: flex;
                gap: 20px;
            }

            .footer-legal a {
                transition: color 0.2s ease;
            }

            .footer-legal a:hover {
                color: #b8f829;
            }

            /* Responsive Utilities */
            @media (max-width: 1024px) {
                .hero__inner {
                    grid-template-columns: 1fr;
                    text-align: center;
                }

                .hero-copy {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }

                .hero-actions {
                    justify-content: center;
                }

                .hero-stats {
                    width: 100%;
                    max-width: 100%;
                }

                .hero-art__wrapper {
                    max-width: 420px;
                }

                .steps-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .step-item:not(:last-child)::after {
                    display: none;
                }

                .split-grid {
                    grid-template-columns: 1fr;
                }

                .promo-block {
                    grid-template-columns: 1fr;
                    padding: 32px;
                }

                .promo-block__right {
                    height: 280px;
                }

                .footer-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .site-header__inner {
                    padding: 12px 16px;
                }

                .nav-links {
                    display: none; /* Mobile menu fallback */
                }

                .hero {
                    padding: 80px 16px 60px;
                }

                .steps-grid {
                    grid-template-columns: 1fr;
                }

                .promo-block__right {
                    display: none;
                }

                .footer-grid {
                    grid-template-columns: 1fr;
                    gap: 32px;
                }

                .footer-bottom {
                    flex-direction: column;
                    text-align: center;
                }
            }
        </style>
    </head>

    <body>
        <div class="page">
            {{-- Header --}}
            <header class="site-header">
                <div class="site-header__inner">
                    <a href="/" class="brand">
                        <span class="brand__mark"></span>
                        <span>BiKuBe</span>
                    </a>

                    <nav class="nav-links">
                        <a href="#categories">{{ __('bikube.public.home.nav_categories') }}</a>
                        <a href="#popular-scenarios">{{ __('bikube.public.home.nav_popular') }}</a>
                        <a href="#how-it-works">{{ __('bikube.public.home.nav_how') }}</a>
                        <a href="#business">{{ __('bikube.public.home.nav_business') }}</a>
                        <a href="#readiness">{{ __('bikube.public.home.nav_status') }}</a>
                    </nav>

                    <div class="header-actions">
                        @auth
                            <a href="/admin" class="button button--secondary">{{ __('bikube.public.home.nav_account') }}</a>
                        @else
                            <a href="/login" class="button button--secondary">{{ __('bikube.public.home.nav_login') }}</a>
                        @endauth
                        <a href="#categories" class="button button--primary">{{ __('bikube.public.home.nav_create_order') }}</a>
                    </div>
                </div>
            </header>

            <main class="page__content">
                {{-- Hero Section --}}
                <section class="hero">
                    <div class="hero__inner">
                        <div class="hero-copy">
                            <div class="badge">
                                <span class="badge__dot"></span>
                                <span>Narvik, Norway</span>
                                <span class="badge__flag"></span>
                            </div>
                            <h1>{{ __('bikube.public.home.hero_title') }} <span>{{ __('bikube.public.home.hero_title_accent') }}</span></h1>
                            <p>{{ __('bikube.public.home.hero_subtitle') }}</p>

                            <div class="hero-actions">
                                <a href="#categories" class="button button--primary">{{ __('bikube.public.home.hero_cta_primary') }}</a>
                                <a href="#readiness" class="button button--secondary">{{ __('bikube.public.home.hero_cta_secondary') }}</a>
                            </div>

                            <div class="hero-stats">
                                <div class="stat-card">
                                    <div class="stat-card__value">10+</div>
                                    <div class="stat-card__label">{{ __('bikube.public.home.stat_directions') }}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__value">Request</div>
                                    <div class="stat-card__label">{{ __('bikube.public.home.stat_request') }}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__value">Narvik</div>
                                    <div class="stat-card__label">{{ __('bikube.public.home.stat_narvik') }}</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__value">✓</div>
                                    <div class="stat-card__label">{{ __('bikube.public.home.stat_honest') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="hero-art">
                            <div class="hero-art__glow"></div>
                            <div class="hero-art__wrapper">
                                <img src="{{ asset('images/bikube/home/hero-main.png') }}" alt="BiKuBe OS" class="hero-art__image" />
                                <div class="hero-art__badge">
                                    <div class="brand__mark"></div>
                                    <div>
                                        <div style="font-weight: 800; font-size: 0.85rem; color: #fff;">BiKuBe Ecosystem</div>
                                        <div style="font-size: 0.75rem; color: #cbd5e1;">Service requests active</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Categories Section --}}
                <section id="categories" class="section">
                    <div class="section__head">
                        <p class="section__eyebrow">{{ __('bikube.public.home.cat_eyebrow') }}</p>
                        <h2 class="section__title">{{ __('bikube.public.home.cat_title') }}</h2>
                        <p class="section__text">{{ __('bikube.public.home.cat_body') }}</p>
                    </div>

                    @php
                        $dbCategories = \App\Models\ServiceCategory::active()
                            ->with(['scenarios' => fn($q) => $q->active()->orderBy('sort_order')])
                            ->orderBy('sort_order')
                            ->get();

                        $categoryIcons = [
                            'delivery' => '🚚',
                            'moving' => '📦',
                            'eco' => '♻️',
                            'handyman' => '🔧',
                            'roadside' => '🚗',
                            'personal-task' => '👤',
                            'classifieds' => '🛒'
                        ];

                        $categoryDescs = [
                            'delivery'      => __('bikube.public.home.cat_desc_delivery'),
                            'moving'        => __('bikube.public.home.cat_desc_moving'),
                            'eco'           => __('bikube.public.home.cat_desc_eco'),
                            'handyman'      => __('bikube.public.home.cat_desc_handyman'),
                            'roadside'      => __('bikube.public.home.cat_desc_roadside'),
                            'personal-task' => __('bikube.public.home.cat_desc_personal_task'),
                            'classifieds'   => __('bikube.public.home.cat_desc_classifieds'),
                        ];
                    @endphp

                    <div class="categories-grid">
                        @foreach($dbCategories as $index => $category)
                            @php
                                $icon = $categoryIcons[$category->slug] ?? '⚡';
                                $desc = $categoryDescs[$category->slug] ?? ($category->description ?: 'Профессиональные услуги в Narvik.');
                                $imgName = $category->slug === 'roadside' ? 'category-tow.png' : ($category->slug === 'personal-task' ? 'category-assistant.png' : 'category-' . $category->slug . '.png');
                                $bgUrl = asset('images/bikube/home/' . $imgName);
                            @endphp

                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div class="category-card" style="background-image: url('{{ $bgUrl }}')">
                                    <div class="category-card__header">
                                        <span class="category-card__num">{{ $index + 1 }}.</span>
                                        <span style="font-size: 1.8rem;">{{ $icon }}</span>
                                    </div>

                                    <div class="category-card__content">
                                        <h3 class="category-card__title">{{ $category->title }}</h3>
                                        <p class="category-card__desc">{{ $desc }}</p>
                                        <div class="category-card__arrow">→</div>
                                    </div>
                                </div>

                                @if($category->scenarios->isNotEmpty())
                                    <div class="category-scenarios">
                                        @foreach($category->scenarios as $scenario)
                                            <a href="{{ route('public.cms.service-page', ['serviceSlug' => $scenario->slug]) }}" class="category-scenarios__item">
                                                <span>{{ $scenario->title }}</span>
                                                <span class="category-scenarios__arrow">→</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Popular Scenarios Slider --}}
                <section id="popular-scenarios" class="section popular-scenarios" style="padding: 48px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; gap: 16px;">
                        <div>
                            <p class="section__eyebrow" style="margin-bottom: 6px;">{{ __('bikube.public.home.popular_eyebrow') }}</p>
                            <h2 class="section__title" style="font-size: clamp(1.8rem, 3vw, 2.5rem);">{{ __('bikube.public.home.popular_title') }}</h2>
                        </div>
                        <div class="bk-row-nav">
                            <button type="button" onclick="bkScroll('scenarios-slider', -1)" aria-label="Назад" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(160, 194, 239, 0.25); color: #d8eafc; background: rgba(5, 17, 40, 0.72); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 900;">←</button>
                            <button type="button" onclick="bkScroll('scenarios-slider', 1)" aria-label="Вперед" style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(160, 194, 239, 0.25); color: #d8eafc; background: rgba(5, 17, 40, 0.72); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 900;">→</button>
                        </div>
                    </div>

                    @php
                        $featuredScenarios = [
                            ['title' => __('bikube.public.home.popular_grocery'),     'slug' => 'delivery-groceries',    'img' => 'slide-groceries.png'],
                            ['title' => __('bikube.public.home.popular_meals'),       'slug' => 'delivery-meals',        'img' => 'slide-meals.png'],
                            ['title' => __('bikube.public.home.popular_bulky'),       'slug' => 'delivery-bulky',        'img' => 'slide-bulky.png'],
                            ['title' => __('bikube.public.home.popular_moving'),      'slug' => 'moving-home',           'img' => 'category-moving.png'],
                            ['title' => __('bikube.public.home.popular_assembly'),    'slug' => 'handyman-assembly',     'img' => 'category-handyman.png'],
                            ['title' => __('bikube.public.home.popular_eco'),         'slug' => 'eco-disposal',          'img' => 'category-eco.png'],
                            ['title' => __('bikube.public.home.popular_tow'),         'slug' => 'tow-emergency',         'img' => 'banner-roadside.png'],
                            ['title' => __('bikube.public.home.popular_classifieds'), 'slug' => 'classifieds-delivery',  'img' => 'scenario-classifieds.png'],
                        ];
                    @endphp

                    <div id="scenarios-slider" class="bk-horizontal">
                        @foreach($featuredScenarios as $scen)
                            <a href="{{ route('public.cms.service-page', ['serviceSlug' => $scen['slug']]) }}" class="scenario-card" style="background-image: url('{{ asset('images/bikube/home/' . $scen['img']) }}')">
                                <span class="scenario-card__title">{{ $scen['title'] }}</span>
                                <div class="scenario-card__footer">
                                    <span class="scenario-card__action">{{ __('bikube.public.home.popular_order_btn') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- How it works Section --}}
                <section id="how-it-works" class="section">
                    <div class="section__head" style="text-align: center; margin: 0 auto 48px;">
                        <p class="section__eyebrow">{{ __('bikube.public.home.how_eyebrow') }}</p>
                        <h2 class="section__title">{{ __('bikube.public.home.how_title') }}</h2>
                        <p class="section__text">{{ __('bikube.public.home.how_body') }}</p>
                    </div>

                    <div class="steps-grid">
                        <div class="step-item">
                            <div class="step-icon">🔍</div>
                            <h3>{{ __('bikube.public.home.step1_title') }}</h3>
                            <p>{{ __('bikube.public.home.step1_body') }}</p>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">📋</div>
                            <h3>{{ __('bikube.public.home.step2_title') }}</h3>
                            <p>{{ __('bikube.public.home.step2_body') }}</p>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">💳</div>
                            <h3>{{ __('bikube.public.home.step3_title') }}</h3>
                            <p>{{ __('bikube.public.home.step3_body') }}</p>
                        </div>
                        <div class="step-item" style="background: linear-gradient(180deg, rgba(184, 248, 41, 0.05), rgba(4, 14, 33, 0.5)); border-color: rgba(184, 248, 41, 0.25);">
                            <div class="step-icon" style="background: rgba(184, 248, 41, 0.15); border-color: #b8f829; color: #b8f829;">✨</div>
                            <h3>{{ __('bikube.public.home.step4_title') }}</h3>
                            <p>{{ __('bikube.public.home.step4_body') }}</p>
                        </div>
                    </div>
                </section>

                {{-- Banners split grids --}}
                <section class="section split-grid" style="padding-top: 0;">
                    <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'classifieds-delivery']) }}" class="banner-card" style="background-image: url('{{ asset('images/bikube/home/scenario-classifieds.png') }}')">
                        <div class="banner-card__content">
                            <h3 class="banner-card__title">{{ __('bikube.public.home.banner_classifieds_title') }}</h3>
                            <p class="banner-card__text">{{ __('bikube.public.home.banner_classifieds_body') }}</p>
                        </div>
                        <span class="banner-card__link">{{ __('bikube.public.home.banner_classifieds_cta') }}</span>
                    </a>

                    <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'tow-emergency']) }}" class="banner-card" style="background-image: url('{{ asset('images/bikube/home/banner-roadside.png') }}')">
                        <div class="banner-card__content">
                            <h3 class="banner-card__title">{{ __('bikube.public.home.banner_roadside_title') }}</h3>
                            <p class="banner-card__text">{{ __('bikube.public.home.banner_roadside_body') }}</p>
                        </div>
                        <span class="banner-card__link">{{ __('bikube.public.home.banner_roadside_cta') }}</span>
                    </a>
                </section>

                <section class="section split-grid" style="padding-top: 0;">
                    <div class="banner-card" style="background-image: url('{{ asset('images/bikube/home/banner-it.png') }}')">
                        <div class="banner-card__content">
                            <h3 class="banner-card__title">{{ __('bikube.public.home.banner_coming_title') }}</h3>
                            <p class="banner-card__text">{{ __('bikube.public.home.banner_coming_body') }}</p>
                        </div>
                        <span class="banner-card__link">{{ __('bikube.public.home.banner_coming_cta') }}</span>
                    </div>

                    <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'delivery-meals']) }}" class="banner-card" style="background-image: url('{{ asset('images/bikube/home/banner-glf-mat.png') }}')">
                        <div class="banner-card__content">
                            <h3 class="banner-card__title">{{ __('bikube.public.home.banner_meals_title') }}</h3>
                            <p class="banner-card__text">{{ __('bikube.public.home.banner_meals_body') }}</p>
                        </div>
                        <span class="banner-card__link">{{ __('bikube.public.home.banner_meals_cta') }}</span>
                    </a>
                </section>

                {{-- Business and Partners --}}
                <section id="business" class="section split-grid" style="padding-top: 0;">
                    <div class="banner-card" style="background-image: url('{{ asset('images/bikube/home/banner-business.png') }}')">
                        <div class="banner-card__content">
                            <h3 class="banner-card__title">{{ __('bikube.public.home.banner_business_title') }}</h3>
                            <p class="banner-card__text">{{ __('bikube.public.home.banner_business_body') }}</p>
                        </div>
                        <span class="banner-card__link">{{ __('bikube.public.home.banner_business_cta') }}</span>
                    </div>

                    <div id="partners" class="banner-card" style="background-image: url('{{ asset('images/bikube/home/banner-partners.png') }}')">
                        <div class="banner-card__content">
                            <h3 class="banner-card__title">{{ __('bikube.public.home.banner_worker_title') }}</h3>
                            <p class="banner-card__text">{{ __('bikube.public.home.banner_worker_body') }}</p>
                        </div>
                        <span class="banner-card__link">{{ __('bikube.public.home.banner_worker_cta') }}</span>
                    </div>
                </section>

                {{-- Product readiness section --}}
                <section id="readiness" class="section" style="padding-top: 0;">
                    <div class="promo-block">
                        <div class="promo-block__left">
                            <h3>{{ __('bikube.public.home.readiness_title') }}</h3>
                            <p>{{ __('bikube.public.home.readiness_body') }}</p>

                            <div class="store-buttons">
                                <span class="store-button" aria-disabled="true">{{ __('bikube.public.home.readiness_mobile_na') }}</span>
                                <span class="store-button" aria-disabled="true">{{ __('bikube.public.home.readiness_partner_na') }}</span>
                            </div>
                        </div>

                        <div class="promo-block__right">
                            <div class="promo-card promo-card--phone" style="background-image: url('{{ asset('images/bikube/home/set1/img-12.png') }}')"></div>
                            <div class="promo-card promo-card--panel" style="background-image: url('{{ asset('images/bikube/home/set1/img-13.png') }}')"></div>
                        </div>
                    </div>
                </section>
            </main>

            {{-- Footer --}}
            <footer id="footer" class="footer">
                <div class="footer__inner">
                    <div class="footer-grid">
                        <div class="footer-col" style="grid-column: span 1.5;">
                            <a href="/" class="brand" style="margin-bottom: 20px;">
                                <span class="brand__mark"></span>
                                <span>BiKuBe</span>
                            </a>
                            <p style="line-height: 1.6; font-size: 0.9rem;">{{ __('bikube.public.home.footer_tagline') }}</p>
                        </div>

                        <div class="footer-col">
                            <h4>{{ __('bikube.public.home.footer_services_title') }}</h4>
                            <div class="footer-links">
                                <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'delivery-groceries']) }}">{{ __('bikube.public.home.footer_delivery') }}</a>
                                <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'moving-home']) }}">{{ __('bikube.public.home.footer_moving') }}</a>
                                <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'handyman-hourly']) }}">{{ __('bikube.public.home.footer_handyman') }}</a>
                                <a href="{{ route('public.cms.service-page', ['serviceSlug' => 'eco-disposal']) }}">{{ __('bikube.public.home.footer_eco') }}</a>
                            </div>
                        </div>

                        <div class="footer-col">
                            <h4>{{ __('bikube.public.home.footer_status_title') }}</h4>
                            <div class="footer-links">
                                <a href="#readiness">{{ __('bikube.public.home.footer_platform') }}</a>
                                <a href="#business">{{ __('bikube.public.home.footer_business') }}</a>
                                <a href="/login">{{ __('bikube.public.home.footer_login') }}</a>
                            </div>
                        </div>

                        <div class="footer-col">
                            <h4>{{ __('bikube.public.home.footer_region_title') }}</h4>
                            <div class="footer-contact">
                                <div class="footer-contact__item">
                                    <span class="footer-contact__icon">📍</span>
                                    <span>Narvik, Norway</span>
                                </div>
                                <div class="footer-contact__item"><span>{{ __('bikube.public.home.footer_contact_na') }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-bottom">
                        <span>{{ __('bikube.public.home.footer_copy', ['year' => date('Y')]) }}</span>
                        <div class="footer-legal">
                            <span>{{ __('bikube.public.home.footer_legal_na') }}</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <script>
            function bkScroll(id, direction) {
                const row = document.getElementById(id);
                if (!row) return;
                row.scrollBy({ left: direction * 280, behavior: 'smooth' });
            }
        </script>
    </body>
</html>
