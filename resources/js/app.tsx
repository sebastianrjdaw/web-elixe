import '../css/app.css';
import 'leaflet/dist/leaflet.css';
import './bootstrap';
import { createInertiaApp, Head, Link as InertiaLink, useForm, usePage } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { ArrowDown, ArrowRight, Building2, Check, ChevronRight, Globe2, MapPin, Menu, Monitor, Moon, Radio, Send, ShieldCheck, Sparkles, Store, Sun, Users, Waves, X } from 'lucide-react';
import React, { lazy, PropsWithChildren, Suspense, useEffect, useMemo, useRef, useState } from 'react';

type Screen = {
    id: string;
    name: string;
    municipality?: string;
    province?: string;
    latitude?: number;
    longitude?: number;
    locationType?: string;
    locationSector?: string;
    commercialStatus?: string;
};

const LazyScreensMap = lazy(() => import('./components/ScreensMap'));

type Paginator<T> = {
    data: T[];
    links?: { url: string | null; label: string; active: boolean }[];
};

type AdminLead = {
    id: number;
    type: string;
    status: string;
    name: string;
    contactName: string;
    email: string;
    phone?: string;
    province?: string;
    municipality?: string;
    message?: string;
    activitySector?: string;
    interestZone?: string;
    budgetRange?: string;
    locationType?: string;
    hasScreen?: boolean;
    wantsElixeScreen?: boolean;
    wantsAdControl?: boolean;
    preferredContactMethod?: string;
    preferredCallTime?: string;
    locale: string;
    screens: string[];
    createdAt?: string;
    lastAction?: string;
    activities: { id: number; action: string; description: string; user: string; createdAt?: string }[];
};

type ResponseTemplate = {
    id: number;
    key: string;
    name: string;
    lead_type?: string | null;
    locale: string;
    subject: string;
    body?: string;
    is_active?: boolean;
};

type AdminScreen = {
    id: number;
    internalName: string;
    publicName?: string;
    municipality?: string;
    province?: string;
    locationType?: string;
    locationSector?: string;
    webVisible: boolean;
    commercialStatus?: string;
    online: boolean;
    syncedAt?: string;
    localVisibilityOverride: boolean | null;
    isVisiblePublicly: boolean;
    missingFields: string[];
    visibilityBlockers: string[];
    warning?: string;
};

type ContentBlock = {
    id: number;
    key: string;
    title_es?: string;
    title_gl?: string;
    subtitle_es?: string;
    subtitle_gl?: string;
    content_es?: string;
    content_gl?: string;
    active: boolean;
    sort_order: number;
    title?: string;
    subtitle?: string;
    content?: string;
};

type Faq = {
    id: number;
    category: string;
    question_es?: string;
    question_gl?: string;
    answer_es?: string;
    answer_gl?: string;
    active?: boolean;
    sort_order?: number;
    question?: string;
    answer?: string;
};

type Legal = {
    id?: number;
    slug: string;
    title_es?: string;
    title_gl?: string;
    content_es?: string;
    content_gl?: string;
    active?: boolean;
    title?: string;
    content?: string;
};

type Setting = {
    id: number;
    key: string;
    value?: string;
    label?: string;
    type: string;
    is_public: boolean;
};

type SharedPageProps = {
    locale: 'es' | 'gl';
    appUrl: string;
    flash?: { success?: string; error?: string };
    [key: string]: unknown;
};

function localizedPath(href: string, locale: 'es' | 'gl'): string {
    if (!href.startsWith('/') || href.startsWith('/admin') || href.startsWith('/api')) {
        return href;
    }

    const normalized = href.replace(/^\/gl(?=\/|\?|$)/, '') || '/';

    return locale === 'gl' ? `/gl${normalized === '/' ? '' : normalized}` : normalized;
}

function useLocale(): 'es' | 'gl' {
    return usePage<SharedPageProps>().props.locale || 'es';
}

function useTranslation() {
    const locale = useLocale();

    return (spanish: string, galician: string) => locale === 'gl' ? galician : spanish;
}

function Link(props: React.ComponentProps<typeof InertiaLink>) {
    const locale = useLocale();
    const href = typeof props.href === 'string' ? localizedPath(props.href, locale) : props.href;

    return <InertiaLink {...props} href={href} />;
}

function Seo({ title, description }: { title: string; description: string }) {
    const page = usePage<SharedPageProps>();
    const locale = page.props.locale || 'es';
    const appUrl = page.props.appUrl || '';
    const path = page.url.split('?')[0];
    const spanishPath = path.replace(/^\/gl(?=\/|$)/, '') || '/';
    const galicianPath = `/gl${spanishPath === '/' ? '' : spanishPath}`;
    const canonicalPath = locale === 'gl' ? galicianPath : spanishPath;

    return (
        <Head title={`${title} | Elixe`}>
            <meta head-key="description" name="description" content={description} />
            <meta head-key="og:title" property="og:title" content={`${title} | Elixe`} />
            <meta head-key="og:description" property="og:description" content={description} />
            <meta head-key="og:type" property="og:type" content="website" />
            <meta head-key="og:url" property="og:url" content={`${appUrl}${canonicalPath}`} />
            <link head-key="canonical" rel="canonical" href={`${appUrl}${canonicalPath}`} />
            <link head-key="alternate-es" rel="alternate" hrefLang="es" href={`${appUrl}${spanishPath}`} />
            <link head-key="alternate-gl" rel="alternate" hrefLang="gl" href={`${appUrl}${galicianPath}`} />
        </Head>
    );
}

function Brand() {
    return (
        <span className="brand-mark">
            <span className="brand-icon" aria-hidden="true"><Waves className="h-5 w-5" /></span>
            <span>ELIXE<small>publicidad local</small></span>
        </span>
    );
}

function Layout({ children }: PropsWithChildren) {
    const [dark, setDark] = useState(() => typeof window !== 'undefined' && localStorage.getItem('elixe.theme') === 'dark');
    const [menuOpen, setMenuOpen] = useState(false);
    const page = usePage<SharedPageProps>();
    const tr = useTranslation();
    const flash = page.props.flash;
    const isGalician = page.props.locale === 'gl';
    const [currentPath, currentQuery] = page.url.split('?');
    const spanishPath = currentPath.replace(/^\/gl(?=\/|$)/, '') || '/';
    const languagePath = isGalician ? spanishPath : `/gl${spanishPath === '/' ? '' : spanishPath}`;
    const languageHref = `${languagePath}${currentQuery ? `?${currentQuery}` : ''}`;

    useEffect(() => {
        localStorage.setItem('elixe.theme', dark ? 'dark' : 'light');
    }, [dark]);

    const navigation = [
        { href: '/', label: tr('Inicio', 'Inicio') },
        { href: '/locales', label: tr('Para locales', 'Para locais') },
        { href: '/anunciantes', label: 'Para anunciantes' },
        { href: '/red-de-pantallas', label: tr('Red de pantallas', 'Rede de pantallas') },
    ];

    return (
        <div className={dark ? 'dark site-shell' : 'site-shell'}>
            <header className="site-header">
                <nav className="site-nav" aria-label="Navegación principal">
                    <Link href="/" aria-label="Elixe, ir al inicio"><Brand /></Link>
                    <div className="hidden items-center gap-1 lg:flex">
                        {navigation.map((item) => <Link key={item.href} className="nav-link" href={item.href}>{item.label}</Link>)}
                    </div>
                    <div className="flex items-center gap-2">
                        <Link className="hidden rounded-full bg-cyan-400 px-5 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-300 sm:inline-flex" href="/asesoramiento">{tr('Solicitar asesoramiento', 'Solicitar asesoramento')}</Link>
                        <InertiaLink href={languageHref} className="icon-btn" aria-label={isGalician ? 'Ver en español' : 'Ver en gallego'}><Globe2 className="h-4 w-4" /><span className="sr-only sm:not-sr-only sm:text-xs">{isGalician ? 'ES' : 'GL'}</span></InertiaLink>
                        <button type="button" className="icon-btn" onClick={() => setDark((value) => !value)} aria-label={dark ? 'Activar modo claro' : 'Activar modo oscuro'}>
                            {dark ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
                        </button>
                        <button type="button" className="icon-btn lg:hidden" onClick={() => setMenuOpen((value) => !value)} aria-expanded={menuOpen} aria-controls="mobile-navigation" aria-label="Abrir menú">
                            {menuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                        </button>
                    </div>
                </nav>
                {menuOpen && (
                    <div id="mobile-navigation" className="mobile-nav">
                        {navigation.map((item) => <Link key={item.href} className="mobile-nav-link" href={item.href}>{item.label}<ChevronRight className="h-4 w-4" /></Link>)}
                        <Link className="btn-primary mt-3" href="/asesoramiento">{tr('Solicitar asesoramiento', 'Solicitar asesoramento')}</Link>
                    </div>
                )}
            </header>
            <main>
                {flash?.success && <div className="mx-auto mt-5 max-w-7xl rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{flash.success}</div>}
                {flash?.error && <div className="mx-auto mt-5 max-w-7xl rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{flash.error}</div>}
                {children}
            </main>
            <footer className="site-footer">
                <div className="mx-auto grid max-w-7xl gap-10 px-5 py-12 sm:px-8 md:grid-cols-[1.2fr_.8fr_.8fr]">
                    <div><Brand /><p className="mt-5 max-w-sm text-sm">{tr('Pantallas digitales para conectar negocios, locales y personas en Galicia.', 'Pantallas dixitais para conectar negocios, locais e persoas en Galicia.')}</p></div>
                    <div><h2 className="footer-title">{tr('Descubre Elixe', 'Descubre Elixe')}</h2><div className="mt-4 grid gap-3 text-sm"><Link href="/locales">{tr('Para locales', 'Para locais')}</Link><Link href="/anunciantes">Para anunciantes</Link><Link href="/red-de-pantallas">{tr('Red de pantallas', 'Rede de pantallas')}</Link></div></div>
                    <div><h2 className="footer-title">{tr('Información', 'Información')}</h2><div className="mt-4 grid gap-3 text-sm"><Link href="/privacidad">{tr('Privacidad', 'Privacidade')}</Link><Link href="/cookies">Cookies</Link><Link href="/aviso-legal">Aviso legal</Link></div></div>
                </div>
                <div className="border-t border-white/10"><div className="mx-auto flex max-w-7xl flex-col gap-2 px-5 py-5 text-xs text-slate-400 sm:flex-row sm:justify-between sm:px-8"><span>© {new Date().getFullYear()} Elixe</span><span>{tr('Publicidad local, impacto real.', 'Publicidade local, impacto real.')}</span></div></div>
            </footer>
        </div>
    );
}

function AdminLayout({ children }: PropsWithChildren) {
    const logout = useForm({});
    const flash = usePage<SharedPageProps>().props.flash;

    return (
        <div className="min-h-screen bg-slate-100 text-slate-950">
            <aside className="fixed inset-y-0 left-0 hidden w-64 border-r border-slate-200 bg-white p-5 lg:block">
                <Link href="/admin" className="text-lg font-semibold">Elixe Admin</Link>
                <nav className="mt-8 grid gap-1 text-sm">
                    <Link className="admin-nav" href="/admin">Dashboard</Link>
                    <Link className="admin-nav" href="/admin/leads">Leads</Link>
                    <Link className="admin-nav" href="/admin/screens">Pantallas</Link>
                    <Link className="admin-nav" href="/admin/sync-runs">Sync logs</Link>
                    <Link className="admin-nav" href="/admin/content">Contenido</Link>
                    <Link className="admin-nav" href="/admin/faqs">FAQs</Link>
                    <Link className="admin-nav" href="/admin/legal-pages">Legales</Link>
                    <Link className="admin-nav" href="/admin/response-templates">Plantillas de email</Link>
                    <Link className="admin-nav" href="/admin/settings">Configuracion</Link>
                    <Link className="admin-nav" href="/admin/diagnostics">Diagnosticos</Link>
                </nav>
            </aside>
            <div className="lg:pl-64">
                <header className="flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
                    <nav className="flex max-w-[70vw] gap-2 overflow-x-auto text-sm lg:hidden" aria-label="Navegación administrativa">
                        <Link className="admin-nav" href="/admin">Inicio</Link>
                        <Link className="admin-nav" href="/admin/leads">Leads</Link>
                        <Link className="admin-nav" href="/admin/screens">Pantallas</Link>
                        <Link className="admin-nav" href="/admin/response-templates">Emails</Link>
                        <Link className="admin-nav" href="/admin/diagnostics">Diagnóstico</Link>
                    </nav>
                    <Link className="text-sm text-slate-600" href="/">Ver web</Link>
                    <form onSubmit={(event) => { event.preventDefault(); logout.post('/admin/logout'); }}>
                        <button className="btn-secondary py-2" type="submit">Salir</button>
                    </form>
                </header>
                <main className="mx-auto max-w-6xl px-5 py-8">
                    {flash?.success && <div className="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{flash.success}</div>}
                    {flash?.error && <div className="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{flash.error}</div>}
                    {children}
                </main>
            </div>
        </div>
    );
}

function Badge({ children, tone = 'slate' }: PropsWithChildren<{ tone?: 'slate' | 'green' | 'amber' | 'red' | 'blue' }>) {
    const tones = {
        slate: 'bg-slate-100 text-slate-700',
        green: 'bg-emerald-100 text-emerald-800',
        amber: 'bg-amber-100 text-amber-800',
        red: 'bg-red-100 text-red-800',
        blue: 'bg-sky-100 text-sky-800',
    };

    return <span className={`inline-flex rounded px-2 py-1 text-xs font-semibold ${tones[tone]}`}>{children}</span>;
}

function paginationLabel(label: string) {
    return label
        .replace(/&laquo;/g, '‹')
        .replace(/&raquo;/g, '›')
        .replace(/&amp;/g, '&')
        .replace(/<[^>]*>/g, '');
}

function Pagination<T>({ page }: { page: Paginator<T> }) {
    if (!page.links?.length) {
        return null;
    }

    return (
        <div className="mt-6 flex flex-wrap gap-2">
            {page.links.map((link, index) => link.url ? (
                <Link key={index} href={link.url} className={`rounded border px-3 py-2 text-sm ${link.active ? 'border-sky-700 bg-sky-700 text-white' : 'border-slate-200 bg-white text-slate-700'}`}>{paginationLabel(link.label)}</Link>
            ) : (
                <span key={index} className="rounded border border-slate-200 px-3 py-2 text-sm text-slate-400">{paginationLabel(link.label)}</span>
            ))}
        </div>
    );
}

function Stat({ label, value }: { label: string; value: number }) {
    return <div className="metric"><strong>{value}</strong><span>{label}</span></div>;
}

function ScreenGrid({ screens, selectable = false, selected = [], onToggle }: { screens: Screen[]; selectable?: boolean; selected?: string[]; onToggle?: (id: string) => void }) {
    const tr = useTranslation();

    return (
        <div className="grid gap-4 md:grid-cols-2">
            {screens.map((screen) => (
                <button type="button" key={screen.id} onClick={() => selectable && onToggle?.(screen.id)} className={`screen-card text-left ${selectable ? '' : 'cursor-default'} ${selected.includes(screen.id) ? 'screen-card-selected' : ''}`} aria-pressed={selectable ? selected.includes(screen.id) : undefined}>
                    <div className="flex items-start justify-between gap-3">
                        <div>
                            <h3>{screen.name}</h3>
                        </div>
                        {selectable && selected.includes(screen.id) ? <span className="rounded-full bg-cyan-400 p-1 text-slate-950"><Check className="h-4 w-4" /></span> : <span className="rounded-full bg-sky-100 p-2 text-sky-800"><Monitor className="h-4 w-4" /></span>}
                    </div>
                    <p className="mt-3 flex items-center gap-2 text-sm text-zinc-600"><MapPin className="h-4 w-4" />{[screen.municipality, screen.province].filter(Boolean).join(', ') || tr('Ubicación aproximada', 'Localización aproximada')}</p>
                    <div className="mt-4 flex flex-wrap gap-2 text-xs">
                        <span className="pill">{screen.locationType || 'local'}</span>
                        <span className="pill">{screen.locationSector || 'sector'}</span>
                        <span className="pill">{screen.commercialStatus || 'disponible'}</span>
                    </div>
                </button>
            ))}
            {screens.length === 0 && <p className="rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">{tr('Todavía no hay pantallas públicas sincronizadas.', 'Aínda non hai pantallas públicas sincronizadas.')}</p>}
        </div>
    );
}

function ScreensMap({ screens }: { screens: Screen[] }) {
    const tr = useTranslation();

    return (
        <Suspense fallback={<div className="map-frame animate-pulse bg-sky-100" role="status" aria-label={tr('Cargando mapa', 'Cargando mapa')} />}>
            <LazyScreensMap screens={screens} />
        </Suspense>
    );
}

function Home({ summary, screens = [], contentBlocks = {}, faqs = [] }: { summary: { totalScreens: number; activeScreens: number; availableScreens: number }; screens?: Screen[]; contentBlocks?: Record<string, ContentBlock>; faqs?: Faq[] }) {
    const tr = useTranslation();
    const hero = contentBlocks.hero || {};
    const venues = contentBlocks.venues || {};
    const advertisers = contentBlocks.advertisers || {};
    const how = contentBlocks.how_it_works || {};

    return (
        <Layout>
            <Seo title={tr('Publicidad local en pantallas digitales', 'Publicidade local en pantallas dixitais')} description={tr('Conecta tu local o negocio con clientes cercanos mediante la red de pantallas digitales de Elixe en Galicia.', 'Conecta o teu local ou negocio con clientes próximos mediante a rede de pantallas dixitais de Elixe en Galicia.')} />
            <section className="home-hero">
                <div className="hero-backdrop" aria-hidden="true" />
                <div className="relative mx-auto flex min-h-[690px] max-w-7xl items-center px-5 py-24 sm:px-8 lg:py-32">
                    <div className="max-w-3xl">
                        <span className="eyebrow eyebrow-light"><Radio className="h-4 w-4" /> {tr('Red digital en Galicia', 'Rede dixital en Galicia')}</span>
                        <h1 className="hero-title">{hero.title || tr('Publicidad local en pantallas reales.', 'Publicidade local en pantallas reais.')}</h1>
                        <p className="hero-copy">{hero.subtitle || tr('Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.', 'Elixe instala e xestiona pantallas dixitais en locais para mostrar contido, promocións e publicidade dun xeito sinxelo.')}</p>
                        <div className="mt-9 flex flex-col gap-3 sm:flex-row">
                            <Link className="btn-primary btn-large" href="/asesoramiento?tipo=venue">{tr('Quiero una pantalla', 'Quero unha pantalla')} <ArrowRight className="h-4 w-4" /></Link>
                            <Link className="btn-ghost btn-large" href="/asesoramiento?tipo=advertiser">{tr('Quiero anunciarme', 'Quero anunciarme')}</Link>
                            <Link className="btn-ghost btn-large" href="/red-de-pantallas"><MapPin className="h-4 w-4" /> {tr('Ver la red', 'Ver a rede')}</Link>
                        </div>
                    </div>
                </div>
                <a href="#que-hacemos" className="hero-scroll" aria-label="Ir a la siguiente sección"><ArrowDown className="h-5 w-5" /></a>
            </section>
            <section id="que-hacemos" className="section section-roomy">
                <div className="grid gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-end">
                    <div><span className="eyebrow">{tr('Qué hace Elixe', 'Que fai Elixe')}</span><h2 className="display-title mt-4">{tr('Una red que conecta el comercio local.', 'Unha rede que conecta o comercio local.')}</h2></div>
                    <p className="text-lg">{tr('Convertimos pantallas en puntos de comunicación útiles. Tu local informa mejor; tu negocio se anuncia donde están sus clientes. Nosotros coordinamos la tecnología, el contenido y la red.', 'Convertemos pantallas en puntos de comunicación útiles. O teu local informa mellor; o teu negocio anúnciase onde está a súa clientela. Coordinamos a tecnoloxía, o contido e a rede.')}</p>
                </div>
                <div className="mt-12 grid gap-5 md:grid-cols-3">
                    <article className="feature-card"><span className="feature-number">01</span><Monitor className="feature-icon" /><h3>{tr('Pantallas reales', 'Pantallas reais')}</h3><p>{tr('Instaladas en establecimientos de la red y gestionadas por Elixe.', 'Instaladas en establecementos da rede e xestionadas por Elixe.')}</p></article>
                    <article className="feature-card"><span className="feature-number">02</span><MapPin className="feature-icon" /><h3>{tr('Impacto de proximidad', 'Impacto de proximidade')}</h3><p>{tr('Campañas por zona y tipo de local para llegar a una audiencia relevante.', 'Campañas por zona e tipo de local para chegar a unha audiencia relevante.')}</p></article>
                    <article className="feature-card"><span className="feature-number">03</span><Sparkles className="feature-icon" /><h3>{tr('Gestión sencilla', 'Xestión sinxela')}</h3><p>{tr('Te acompañamos desde la idea hasta la publicación y el mantenimiento.', 'Acompañámoste desde a idea ata a publicación e o mantemento.')}</p></article>
                </div>
            </section>
            <section className="network-stats bg-slate-950 py-6 dark:bg-black">
                <div className="mx-auto grid max-w-7xl grid-cols-3 gap-2 px-5 sm:px-8">
                    <Stat label={tr('pantallas en red', 'pantallas en rede')} value={summary.totalScreens} />
                    <Stat label={tr('pantallas activas', 'pantallas activas')} value={summary.activeScreens} />
                    <Stat label={tr('disponibles', 'dispoñibles')} value={summary.availableScreens} />
                </div>
            </section>
            <section className="section section-roomy">
                <div className="section-heading"><span className="eyebrow">{tr('Dos formas de conectar', 'Dúas formas de conectar')}</span><h2 className="display-title">{tr('Elixe trabaja para ti.', 'Elixe traballa para ti.')}</h2></div>
                <div className="mt-10 grid gap-6 lg:grid-cols-2">
                    <article className="audience-card audience-venues">
                        <div className="audience-content"><span className="card-tag"><Store className="h-4 w-4" /> {tr('Para locales', 'Para locais')}</span><h3>{venues.title || tr('Haz que tu pantalla trabaje para tu local', 'Fai que a túa pantalla traballe para o teu local')}</h3><p>{venues.content || tr('Muestra promociones, menús, avisos o eventos mientras formas parte de una red publicitaria local.', 'Mostra promocións, menús, avisos ou eventos mentres formas parte dunha rede publicitaria local.')}</p><Link href="/locales">{tr('Descubrir ventajas', 'Descubrir vantaxes')} <ArrowRight className="h-4 w-4" /></Link></div>
                    </article>
                    <article className="audience-card audience-advertisers">
                        <div className="audience-content"><span className="card-tag"><Building2 className="h-4 w-4" /> Para anunciantes</span><h3>{advertisers.title || tr('Tu negocio, delante de clientes cercanos', 'O teu negocio, diante de clientes próximos')}</h3><p>{advertisers.content || tr('Amplía tu visibilidad en pantallas reales, seleccionando zonas y tipos de establecimientos.', 'Amplía a túa visibilidade en pantallas reais, seleccionando zonas e tipos de establecementos.')}</p><Link href="/anunciantes">{tr('Planificar una campaña', 'Planificar unha campaña')} <ArrowRight className="h-4 w-4" /></Link></div>
                    </article>
                </div>
            </section>
            <section className="soft-section">
                <div className="section section-roomy">
                    <div className="grid gap-12 lg:grid-cols-[.8fr_1.2fr]">
                        <div><span className="eyebrow">{how.title || tr('Cómo funciona', 'Como funciona')}</span><h2 className="display-title mt-4">{tr('De la primera conversación a la pantalla.', 'Da primeira conversa á pantalla.')}</h2><p className="mt-5">{how.content || tr('Recogemos tu solicitud, revisamos el encaje y preparamos una propuesta personalizada.', 'Recibimos a túa solicitude, revisamos o encaixe e preparamos unha proposta personalizada.')}</p><Link className="btn-secondary mt-7" href="/asesoramiento">{tr('Cuéntanos tu idea', 'Cóntanos a túa idea')} <ArrowRight className="h-4 w-4" /></Link></div>
                        <ol className="process-list">
                            <li><span>1</span><div><h3>{tr('Cuéntanos qué necesitas', 'Cóntanos que necesitas')}</h3><p>{tr('Local, campaña u otra consulta: un único formulario adaptado a ti.', 'Local, campaña ou outra consulta: un único formulario adaptado a ti.')}</p></div></li>
                            <li><span>2</span><div><h3>{tr('Diseñamos la propuesta', 'Deseñamos a proposta')}</h3><p>{tr('Revisamos zonas, pantallas, objetivos y disponibilidad contigo.', 'Revisamos zonas, pantallas, obxectivos e dispoñibilidade contigo.')}</p></div></li>
                            <li><span>3</span><div><h3>{tr('Lo ponemos en marcha', 'Poñémolo en marcha')}</h3><p>{tr('Elixe gestiona la configuración, la publicación y el seguimiento.', 'Elixe xestiona a configuración, a publicación e o seguimento.')}</p></div></li>
                        </ol>
                    </div>
                </div>
            </section>
            <section className="section section-roomy">
                <div className="section-head"><div><span className="eyebrow">{tr('Red Elixe', 'Rede Elixe')}</span><h2 className="display-title mt-4">Pantallas en Galicia</h2><p className="mt-4">{tr('Explora ubicaciones reales de nuestra red y encuentra el espacio adecuado.', 'Explora localizacións reais da nosa rede e atopa o espazo axeitado.')}</p></div><Link className="btn-secondary" href="/red-de-pantallas">{tr('Ver toda la red', 'Ver toda a rede')} <ArrowRight className="h-4 w-4" /></Link></div>
                <div className="map-panel mt-8"><ScreensMap screens={screens} /></div>
            </section>
            {faqs.length > 0 && (
                <section className="soft-section"><div className="section section-roomy">
                    <div className="section-heading"><span className="eyebrow">{tr('Resolvemos tus dudas', 'Resolvemos as túas dúbidas')}</span><h2 className="display-title">{tr('Preguntas frecuentes', 'Preguntas frecuentes')}</h2></div>
                    <div className="mt-10 grid gap-4 md:grid-cols-2">
                        {faqs.map((faq) => (
                            <article key={faq.id} className="faq-card">
                                <h3>{faq.question}</h3>
                                <p className="mt-3">{faq.answer}</p>
                            </article>
                        ))}
                    </div>
                </div></section>
            )}
            <section className="cta-section"><div className="section py-20 text-center"><span className="eyebrow eyebrow-light">{tr('Hablemos', 'Falemos')}</span><h2 className="mx-auto mt-4 max-w-3xl text-4xl font-bold text-white md:text-5xl">{tr('Tu próxima conexión local empieza aquí.', 'A túa próxima conexión local comeza aquí.')}</h2><p className="mx-auto mt-5 max-w-2xl text-sky-100">{tr('Cuéntanos qué necesitas. Te ayudaremos a encontrar la solución adecuada, sin compromiso.', 'Cóntanos que necesitas. Axudarémosche a atopar a solución axeitada, sen compromiso.')}</p><Link className="btn-primary btn-large mt-8" href="/asesoramiento">{tr('Solicitar asesoramiento', 'Solicitar asesoramento')} <ArrowRight className="h-4 w-4" /></Link></div></section>
        </Layout>
    );
}

function ScreensPage({ screens }: { screens: Screen[] }) {
    const tr = useTranslation();
    const [sector, setSector] = useState('');
    const [locationType, setLocationType] = useState('');
    const [selected, setSelected] = useState<string[]>(() => {
        try {
            const stored = JSON.parse(sessionStorage.getItem('elixe.selectedScreens') || '[]');
            return Array.isArray(stored) ? stored.filter((value): value is string => typeof value === 'string') : [];
        } catch {
            return [];
        }
    });
    const sectors = useMemo(() => [...new Set(screens.map((screen) => screen.locationSector).filter(Boolean))], [screens]);
    const locationTypes = useMemo(() => [...new Set(screens.map((screen) => screen.locationType).filter(Boolean))], [screens]);
    const filtered = screens.filter((screen) => (!sector || screen.locationSector === sector) && (!locationType || screen.locationType === locationType));
    const toggleScreen = (id: string) => {
        const next = selected.includes(id) ? selected.filter((value) => value !== id) : [...selected, id];
        setSelected(next);
        sessionStorage.setItem('elixe.selectedScreens', JSON.stringify(next));
    };

    return (
        <Layout>
            <Seo title={tr('Red de pantallas', 'Rede de pantallas')} description={tr('Explora las pantallas digitales disponibles de Elixe por zona y tipo de establecimiento.', 'Explora as pantallas dixitais dispoñibles de Elixe por zona e tipo de establecemento.')} />
            <section className="page-hero"><div className="section py-16 md:py-24"><span className="eyebrow eyebrow-light"><MapPin className="h-4 w-4" /> {tr('Red Elixe', 'Rede Elixe')}</span><h1 className="mt-5 max-w-4xl text-white">{tr('Publicidad donde sucede la vida local.', 'Publicidade onde sucede a vida local.')}</h1><p className="mt-5 max-w-2xl text-lg text-sky-100">{tr('Explora las pantallas disponibles en Galicia, filtra por sector y elige dónde quieres que se vea tu negocio.', 'Explora as pantallas dispoñibles en Galicia, filtra por sector e elixe onde queres que se vexa o teu negocio.')}</p></div></section>
            <section className="section section-roomy">
                <div className="filter-bar">
                    <div><strong>{filtered.length}</strong><span> {tr('pantallas disponibles', 'pantallas dispoñibles')}</span></div>
                    <label className="sr-only" htmlFor="sector-filter">Filtrar por sector</label>
                    <select id="sector-filter" className="input max-w-xs" value={sector} onChange={(event) => setSector(event.target.value)}>
                        <option value="">{tr('Todos los sectores', 'Todos os sectores')}</option>
                        {sectors.map((item) => <option key={item} value={item}>{item}</option>)}
                    </select>
                    <label className="sr-only" htmlFor="type-filter">Filtrar por tipo de local</label>
                    <select id="type-filter" className="input max-w-xs" value={locationType} onChange={(event) => setLocationType(event.target.value)}>
                        <option value="">{tr('Todos los tipos', 'Todos os tipos')}</option>
                        {locationTypes.map((item) => <option key={item} value={item}>{item}</option>)}
                    </select>
                </div>
                <div className="map-panel mt-8"><ScreensMap screens={filtered} /></div>
                <div className="mt-12 flex items-end justify-between gap-6"><div><span className="eyebrow">{tr('Elige ubicaciones', 'Elixe localizacións')}</span><h2 className="mt-3">{tr('Selecciona tus pantallas', 'Selecciona as túas pantallas')}</h2><p className="mt-2">{tr('Puedes marcar una o varias y continuar al formulario.', 'Podes marcar unha ou varias e continuar ao formulario.')}</p></div>{selected.length > 0 && <span className="selection-count">{selected.length} {tr('seleccionada', 'seleccionada')}{selected.length === 1 ? '' : 's'}</span>}</div>
                <div className="mt-6"><ScreenGrid screens={filtered} selectable selected={selected} onToggle={toggleScreen} /></div>
            </section>
            <div className={`selection-dock ${selected.length ? 'selection-dock-visible' : ''}`} aria-live="polite"><div><strong>{selected.length} pantalla{selected.length === 1 ? '' : 's'}</strong><span className="hidden text-sm text-slate-500 sm:inline"> {tr('seleccionadas para tu campaña', 'seleccionadas para a túa campaña')}</span></div><Link href="/asesoramiento?tipo=advertiser" className="btn-primary">{tr('Continuar', 'Continuar')} <ArrowRight className="h-4 w-4" /></Link></div>
        </Layout>
    );
}

function VenuesPage() {
    const tr = useTranslation();

    return (
        <Layout>
            <Seo title={tr('Pantallas para locales', 'Pantallas para locais')} description={tr('Convierte la pantalla de tu establecimiento en un canal útil, gestionado y conectado a la red local de Elixe.', 'Converte a pantalla do teu establecemento nunha canle útil, xestionada e conectada á rede local de Elixe.')} />
            <section className="page-hero"><div className="section py-16 md:py-24"><span className="eyebrow eyebrow-light"><Store className="h-4 w-4" /> {tr('Para locales', 'Para locais')}</span><h1 className="mt-5 max-w-4xl text-white">{tr('Una pantalla útil para tu negocio.', 'Unha pantalla útil para o teu negocio.')}</h1><p className="mt-5 max-w-2xl text-lg text-sky-100">{tr('Comunica promociones, menús y avisos, con instalación y gestión acompañadas por Elixe.', 'Comunica promocións, menús e avisos, con instalación e xestión acompañadas por Elixe.')}</p><Link className="btn-primary btn-large mt-8" href="/asesoramiento?tipo=venue">{tr('Valorar mi local', 'Valorar o meu local')} <ArrowRight className="h-4 w-4" /></Link></div></section>
            <section className="section section-roomy"><div className="grid gap-5 md:grid-cols-3"><article className="feature-card"><Monitor className="feature-icon" /><h2>{tr('Contenido propio', 'Contido propio')}</h2><p>{tr('Informa a tus clientes con piezas adaptadas a tu establecimiento.', 'Informa á túa clientela con pezas adaptadas ao teu establecemento.')}</p></article><article className="feature-card"><Sparkles className="feature-icon" /><h2>{tr('Sin complicaciones', 'Sen complicacións')}</h2><p>{tr('Te ayudamos con la pantalla, la configuración y el mantenimiento.', 'Axudámosche coa pantalla, a configuración e o mantemento.')}</p></article><article className="feature-card"><Users className="feature-icon" /><h2>{tr('Red local', 'Rede local')}</h2><p>{tr('Forma parte de una red publicitaria pensada para negocios cercanos.', 'Forma parte dunha rede publicitaria pensada para negocios próximos.')}</p></article></div></section>
            <section className="soft-section"><div className="section section-roomy grid gap-12 lg:grid-cols-[.8fr_1.2fr]"><div><span className="eyebrow">{tr('Tú mantienes el control', 'Ti mantés o control')}</span><h2 className="display-title mt-4">{tr('Contenido propio y publicidad autorizada.', 'Contido propio e publicidade autorizada.')}</h2><p className="mt-5">{tr('Elixe revisa y programa los anuncios. Tu local conserva espacio para sus mensajes y puede indicar categorías publicitarias no permitidas. La participación puede generar ingresos adicionales según las condiciones acordadas para cada instalación.', 'Elixe revisa e programa os anuncios. O teu local conserva espazo para as súas mensaxes e pode indicar categorías publicitarias non permitidas. A participación pode xerar ingresos adicionais segundo as condicións acordadas para cada instalación.')}</p></div><ol className="process-list"><li><span>1</span><div><h3>{tr('Cuéntanos cómo es tu local', 'Cóntanos como é o teu local')}</h3><p>{tr('Ubicación, tipo de actividad y pantalla actual.', 'Localización, tipo de actividade e pantalla actual.')}</p></div></li><li><span>2</span><div><h3>{tr('Evaluamos pantalla y ubicación', 'Avaliamos pantalla e localización')}</h3><p>{tr('Comprobamos el encaje técnico y comercial contigo.', 'Comprobamos o encaixe técnico e comercial contigo.')}</p></div></li><li><span>3</span><div><h3>{tr('Configuramos y gestionamos', 'Configuramos e xestionamos')}</h3><p>{tr('Elixe acompaña la instalación, contenidos y mantenimiento.', 'Elixe acompaña a instalación, os contidos e o mantemento.')}</p></div></li><li><span>4</span><div><h3>{tr('Publicas con control', 'Publicas con control')}</h3><p>{tr('Contenido propio y publicidad dentro de las categorías acordadas.', 'Contido propio e publicidade dentro das categorías acordadas.')}</p></div></li></ol></div></section>
        </Layout>
    );
}

function AdvertisersPage({ screens }: { screens: Screen[] }) {
    const tr = useTranslation();

    return (
        <Layout>
            <Seo title={tr('Publicidad para anunciantes', 'Publicidade para anunciantes')} description={tr('Planifica campañas de proximidad en pantallas reales de Galicia con acompañamiento comercial de Elixe.', 'Planifica campañas de proximidade en pantallas reais de Galicia con acompañamento comercial de Elixe.')} />
            <section className="page-hero"><div className="section py-16 md:py-24"><span className="eyebrow eyebrow-light"><Building2 className="h-4 w-4" /> Para anunciantes</span><h1 className="mt-5 max-w-4xl text-white">{tr('Tu negocio, delante de clientes cercanos.', 'O teu negocio, diante de clientes próximos.')}</h1><p className="mt-5 max-w-2xl text-lg text-sky-100">{tr('Planifica campañas por zona y tipo de establecimiento en pantallas reales de Galicia.', 'Planifica campañas por zona e tipo de establecemento en pantallas reais de Galicia.')}</p><Link className="btn-primary btn-large mt-8" href="/asesoramiento?tipo=advertiser">{tr('Pedir una propuesta', 'Pedir unha proposta')} <ArrowRight className="h-4 w-4" /></Link></div></section>
            <section className="section section-roomy"><div className="section-head"><div><span className="eyebrow">{tr('Cobertura disponible', 'Cobertura dispoñible')}</span><h2 className="display-title mt-4">{tr('Explora antes de decidir.', 'Explora antes de decidir.')}</h2><p className="mt-3">{tr('Consulta la red y selecciona las ubicaciones que quieras incluir en tu solicitud.', 'Consulta a rede e selecciona as localizacións que queiras incluír na túa solicitude.')}</p></div><Link className="btn-secondary" href="/red-de-pantallas">{tr('Ver todas las pantallas', 'Ver todas as pantallas')}</Link></div><div className="map-panel mt-8"><ScreensMap screens={screens} /></div></section>
            <section className="soft-section"><div className="section section-roomy grid gap-12 lg:grid-cols-[1.2fr_.8fr]"><ol className="process-list"><li><span>1</span><div><h3>{tr('Elige zona, sector o pantallas', 'Elixe zona, sector ou pantallas')}</h3><p>{tr('Usa el mapa como punto de partida.', 'Usa o mapa como punto de partida.')}</p></div></li><li><span>2</span><div><h3>{tr('Cuéntanos el objetivo', 'Cóntanos o obxectivo')}</h3><p>{tr('Indica campaña, presupuesto y contacto.', 'Indica campaña, orzamento e contacto.')}</p></div></li><li><span>3</span><div><h3>{tr('Recibe una propuesta', 'Recibe unha proposta')}</h3><p>{tr('Revisamos cobertura y disponibilidad real.', 'Revisamos cobertura e dispoñibilidade real.')}</p></div></li><li><span>4</span><div><h3>{tr('Activamos la campaña', 'Activamos a campaña')}</h3><p>{tr('Elixe coordina preparación, publicación y seguimiento.', 'Elixe coordina preparación, publicación e seguimento.')}</p></div></li></ol><div><span className="eyebrow">{tr('Acompañamiento comercial', 'Acompañamento comercial')}</span><h2 className="display-title mt-4">{tr('Una campaña adaptada a tu negocio.', 'Unha campaña adaptada ao teu negocio.')}</h2><p className="mt-5">{tr('Puedes consultar servicios de diseño o adaptación de imágenes y vídeo; su alcance se confirmará en la propuesta y no se considera incluido por defecto.', 'Podes consultar servizos de deseño ou adaptación de imaxes e vídeo; o seu alcance confirmarase na proposta e non se considera incluído por defecto.')}</p><div className="mt-7 flex flex-wrap gap-3"><Link className="btn-primary" href="/asesoramiento?tipo=advertiser">{tr('Solicitar propuesta', 'Solicitar proposta')}</Link><Link className="btn-secondary" href="/asesoramiento?tipo=other&motivo=media-kit">{tr('Solicitar dossier comercial', 'Solicitar dossier comercial')}</Link></div></div></div></section>
        </Layout>
    );
}

declare global {
    interface Window {
        turnstile?: {
            render: (element: HTMLElement, options: Record<string, unknown>) => string;
            remove: (widgetId: string) => void;
        };
    }
}

function TurnstileWidget({ siteKey, onVerify }: { siteKey: string; onVerify: (token: string) => void }) {
    const containerId = 'elixe-turnstile';
    const onVerifyRef = useRef(onVerify);

    useEffect(() => {
        onVerifyRef.current = onVerify;
    }, [onVerify]);

    useEffect(() => {
        let widgetId: string | undefined;
        const renderWidget = () => {
            const container = document.getElementById(containerId);
            if (container && window.turnstile && !container.hasChildNodes()) {
                widgetId = window.turnstile.render(container, {
                    sitekey: siteKey,
                    callback: (token: string) => onVerifyRef.current(token),
                    'expired-callback': () => onVerifyRef.current(''),
                    theme: 'auto',
                });
            }
        };
        const existing = document.querySelector<HTMLScriptElement>('script[data-elixe-turnstile]');
        if (existing) {
            existing.addEventListener('load', renderWidget);
            renderWidget();
        } else {
            const script = document.createElement('script');
            script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
            script.async = true;
            script.defer = true;
            script.dataset.elixeTurnstile = 'true';
            script.addEventListener('load', renderWidget);
            document.head.appendChild(script);
        }

        return () => {
            existing?.removeEventListener('load', renderWidget);
            if (widgetId && window.turnstile) window.turnstile.remove(widgetId);
        };
    }, [siteKey]);

    return <div id={containerId} className="min-h-[65px]" aria-label="Verificación de seguridad" />;
}

function AdvicePage({ screens, turnstileSiteKey }: { screens: Screen[]; turnstileSiteKey?: string | null }) {
    const locale = useLocale();
    const tr = useTranslation();
    const params = new URLSearchParams(window.location.search);
    const [initialSelected] = useState<string[]>(() => {
        try {
            const selected = JSON.parse(sessionStorage.getItem('elixe.selectedScreens') || '[]');
            return Array.isArray(selected) ? selected.filter((value): value is string => typeof value === 'string') : [];
        } catch {
            sessionStorage.removeItem('elixe.selectedScreens');
            return [];
        }
    });
    const requestedType = params.get('tipo');
    const initialType = (initialSelected.length > 0
        ? 'advertiser'
        : (requestedType === 'advertiser' || requestedType === 'other' ? requestedType : 'venue')) as 'venue' | 'advertiser' | 'other';
    const [submissionToken] = useState(() => crypto.randomUUID());
    const form = useForm({
        submission_token: submissionToken,
        type: initialType,
        business_name: '',
        company_name: '',
        contact_name: '',
        email: '',
        phone: '',
        province: '',
        municipality: '',
        location_type: '',
        has_screen: false,
        wants_elixe_screen: false,
        wants_ad_control: false,
        activity_sector: '',
        interest_zone: '',
        budget_range: '',
        selected_screen_ids: initialSelected,
        preferred_contact_method: 'llamada',
        preferred_call_time: 'indiferente',
        message: params.get('motivo') === 'media-kit' ? tr('Me gustaría recibir el dossier comercial.', 'Gustaríame recibir o dossier comercial.') : '',
        privacy_accepted: false,
        cf_turnstile_response: '',
    });

    const selectedScreens = screens.filter((screen) => form.data.selected_screen_ids.includes(screen.id));
    const toggleScreen = (id: string) => form.setData('selected_screen_ids', form.data.selected_screen_ids.includes(id) ? form.data.selected_screen_ids.filter((value) => value !== id) : [...form.data.selected_screen_ids, id]);
    const errors = form.errors as Record<string, string>;
    const error = (field: string) => errors[field] ? <p className="field-error">{errors[field]}</p> : null;
    const inputClass = (field: string) => `input ${errors[field] ? 'input-error' : ''}`;
    const validateClient = () => {
        const nextErrors: Record<string, string> = {};
        const required = (field: keyof typeof form.data, label: string) => {
            const value = form.data[field];
            if (value === null || value === undefined || value === '' || value === false) {
                nextErrors[field] = locale === 'gl' ? `O campo ${label} é obrigatorio.` : `El campo ${label} es obligatorio.`;
            }
        };

        if (form.data.type === 'venue') {
            required('business_name', tr('nombre del local', 'nome do local'));
            required('province', tr('provincia', 'provincia'));
            required('municipality', tr('municipio', 'concello'));
            required('location_type', tr('tipo de local', 'tipo de local'));
        }

        if (form.data.type === 'advertiser') {
            required('company_name', tr('nombre de empresa', 'nome da empresa'));
            required('activity_sector', tr('sector de actividad', 'sector de actividade'));
            required('interest_zone', tr('zona de interés', 'zona de interese'));
            required('budget_range', tr('presupuesto orientativo', 'orzamento orientativo'));
        }

        if (form.data.type === 'other') {
            required('message', tr('mensaje', 'mensaxe'));
        }

        required('contact_name', tr('nombre de contacto', 'nome de contacto'));
        required('phone', tr('teléfono', 'teléfono'));
        required('email', 'email');
        required('preferred_contact_method', tr('preferencia de contacto', 'preferencia de contacto'));
        required('preferred_call_time', tr('horario preferido', 'horario preferido'));
        required('privacy_accepted', tr('política de privacidad', 'política de privacidade'));

        if (form.data.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.data.email)) {
            nextErrors.email = tr('Introduce un email válido.', 'Introduce un correo electrónico válido.');
        }

        if (form.data.phone && !/^(\+34|0034)?[6789]\d{8}$/.test(form.data.phone)) {
            nextErrors.phone = tr('Introduce un teléfono español válido.', 'Introduce un teléfono español válido.');
        }

        if (Object.keys(nextErrors).length > 0) {
            form.setError(nextErrors);

            return false;
        }

        form.clearErrors();

        return true;
    };

    return (
        <Layout>
            <Seo title={tr('Solicitar asesoramiento', 'Solicitar asesoramento')} description={tr('Cuéntanos qué necesita tu local o campaña y recibe una propuesta personalizada del equipo de Elixe.', 'Cóntanos que necesita o teu local ou campaña e recibe unha proposta personalizada do equipo de Elixe.')} />
            <section className="page-hero page-hero-compact"><div className="section py-14 md:py-20"><span className="eyebrow eyebrow-light"><Users className="h-4 w-4" /> {tr('Hablemos', 'Falemos')}</span><h1 className="mt-5 text-white">{tr('Solicitar asesoramiento', 'Solicitar asesoramento')}</h1><p className="mt-4 max-w-2xl text-lg text-sky-100">{tr('Cuéntanos qué necesitas y el equipo de Elixe preparará contigo el siguiente paso.', 'Cóntanos que necesitas e o equipo de Elixe preparará contigo o seguinte paso.')}</p></div></section>
            <section className="section section-roomy">
                <div className="section-heading text-center">
                    <span className="eyebrow">{tr('Paso 1 · Elige tu objetivo', 'Paso 1 · Elixe o teu obxectivo')}</span>
                    <h2 className="display-title">{tr('¿Cómo podemos ayudarte?', 'Como podemos axudarche?')}</h2>
                </div>
                <div className="mx-auto mt-8 grid max-w-5xl gap-4 md:grid-cols-3">
                    <button type="button" className={`choice ${form.data.type === 'venue' ? 'choice-active' : ''}`} onClick={() => { form.setData('type', 'venue'); form.clearErrors(); }}>
                        <Store className="h-5 w-5" />{tr('Tengo un local', 'Teño un local')}
                    </button>
                    <button type="button" className={`choice ${form.data.type === 'advertiser' ? 'choice-active' : ''}`} onClick={() => { form.setData('type', 'advertiser'); form.clearErrors(); }}>
                        <Building2 className="h-5 w-5" />{tr('Quiero anunciarme', 'Quero anunciarme')}
                    </button>
                    <button type="button" className={`choice ${form.data.type === 'other' ? 'choice-active' : ''}`} onClick={() => { form.setData('type', 'other'); form.clearErrors(); }}>
                        <Send className="h-5 w-5" />{tr('Tengo otra consulta', 'Teño outra consulta')}
                    </button>
                </div>
                <div className="mx-auto mt-12 max-w-5xl"><div className="mb-6"><span className="eyebrow">{tr('Paso 2 · Tus datos', 'Paso 2 · Os teus datos')}</span><h2 className="mt-3">{tr('Cuéntanos un poco más', 'Cóntanos un pouco máis')}</h2><p className="mt-2">{tr('Los campos con * son obligatorios.', 'Os campos con * son obrigatorios.')}</p></div>
                <form className="form-panel advice-form" noValidate onSubmit={(event) => { event.preventDefault(); if (validateClient()) form.post(localizedPath('/asesoramiento', locale), { onSuccess: () => sessionStorage.removeItem('elixe.selectedScreens') }); }}>
                    {form.data.type === 'venue' && <div><label htmlFor="business_name">{tr('Nombre del local', 'Nome do local')} *</label><input id="business_name" className={inputClass('business_name')} placeholder={tr('Ej. Café Atlántico', 'Ex. Café Atlántico')} value={form.data.business_name} onChange={(e) => form.setData('business_name', e.target.value)} />{error('business_name')}</div>}
                    {form.data.type === 'advertiser' && <div><label htmlFor="company_name">{tr('Nombre de empresa', 'Nome da empresa')} *</label><input id="company_name" className={inputClass('company_name')} placeholder={tr('Ej. Mi empresa', 'Ex. A miña empresa')} value={form.data.company_name} onChange={(e) => form.setData('company_name', e.target.value)} />{error('company_name')}</div>}
                    <div><label htmlFor="contact_name">{tr('Nombre de contacto', 'Nome de contacto')} *</label><input id="contact_name" className={inputClass('contact_name')} placeholder={tr('Nombre y apellidos', 'Nome e apelidos')} value={form.data.contact_name} onChange={(e) => form.setData('contact_name', e.target.value)} />{error('contact_name')}</div>
                    <div><label htmlFor="phone">Teléfono *</label><input id="phone" className={inputClass('phone')} inputMode="tel" autoComplete="tel" placeholder="600 000 000" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value.replace(/\s/g, ''))} />{error('phone')}</div>
                    <div><label htmlFor="email">Email *</label><input id="email" className={inputClass('email')} type="email" autoComplete="email" placeholder="tu@email.com" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} />{error('email')}</div>
                    <div><label htmlFor="province">{tr('Provincia', 'Provincia')} {form.data.type === 'venue' ? '*' : ''}</label><input id="province" className={inputClass('province')} autoComplete="address-level1" placeholder={tr('Ej. A Coruña', 'Ex. A Coruña')} value={form.data.province} onChange={(e) => form.setData('province', e.target.value)} />{error('province')}</div>
                    <div><label htmlFor="municipality">{tr('Municipio', 'Concello')} {form.data.type === 'venue' ? '*' : ''}</label><input id="municipality" className={inputClass('municipality')} autoComplete="address-level2" placeholder={tr('Ej. Santiago de Compostela', 'Ex. Santiago de Compostela')} value={form.data.municipality} onChange={(e) => form.setData('municipality', e.target.value)} />{error('municipality')}</div>
                    {form.data.type === 'venue' && <div><label htmlFor="location_type">{tr('Tipo de local', 'Tipo de local')} *</label><select id="location_type" className={inputClass('location_type')} value={form.data.location_type} onChange={(e) => form.setData('location_type', e.target.value)}>
                        <option value="">{tr('Tipo de local', 'Tipo de local')}</option>
                        {['bar', 'restaurante', 'cafeteria', 'lavanderia', 'gimnasio', 'peluqueria', 'clinica', 'tienda', 'hotel', 'supermercado', 'oficina', 'centro_comercial', 'farmacia', 'autoescuela', 'estanco', 'panaderia', 'coworking', 'otro'].map((type) => <option key={type} value={type}>{type}</option>)}
                    </select>{error('location_type')}</div>}
                    {form.data.type === 'venue' && <label className="check"><input type="checkbox" checked={form.data.has_screen} onChange={(e) => form.setData('has_screen', e.target.checked)} /> {tr('Tiene pantalla actualmente', 'Ten pantalla actualmente')}</label>}
                    {form.data.type === 'venue' && <label className="check"><input type="checkbox" checked={form.data.wants_elixe_screen} onChange={(e) => form.setData('wants_elixe_screen', e.target.checked)} /> {tr('Quiere que Elixe proporcione pantalla', 'Quere que Elixe proporcione a pantalla')}</label>}
                    {form.data.type === 'venue' && <label className="check"><input type="checkbox" checked={form.data.wants_ad_control} onChange={(e) => form.setData('wants_ad_control', e.target.checked)} /> {tr('Quiere controlar publicidad', 'Quere controlar a publicidade')}</label>}
                    {form.data.type === 'advertiser' && <div><label htmlFor="activity_sector">{tr('Sector de actividad', 'Sector de actividade')} *</label><select id="activity_sector" className={inputClass('activity_sector')} value={form.data.activity_sector} onChange={(e) => form.setData('activity_sector', e.target.value)}>
                        <option value="">{tr('Sector de actividad', 'Sector de actividade')}</option>
                        {['Hosteleria', 'Comercio local', 'Salud y bienestar', 'Servicios profesionales', 'Inmobiliaria', 'Automocion', 'Educacion', 'Eventos', 'Turismo', 'Ocio', 'Otro'].map((sector) => <option key={sector} value={sector}>{sector}</option>)}
                    </select>{error('activity_sector')}</div>}
                    {form.data.type === 'advertiser' && <div><label htmlFor="interest_zone">{tr('Zona de interés', 'Zona de interese')} *</label><input id="interest_zone" className={inputClass('interest_zone')} placeholder={tr('Municipios, barrios o zonas', 'Concellos, barrios ou zonas')} value={form.data.interest_zone} onChange={(e) => form.setData('interest_zone', e.target.value)} />{error('interest_zone')}</div>}
                    {form.data.type === 'advertiser' && <div><label htmlFor="budget_range">{tr('Presupuesto orientativo', 'Orzamento orientativo')} *</label><select id="budget_range" className={inputClass('budget_range')} value={form.data.budget_range} onChange={(e) => form.setData('budget_range', e.target.value)}>
                        <option value="">{tr('Presupuesto orientativo', 'Orzamento orientativo')}</option>
                        <option value="menos_100">{tr('Menos de 100 EUR', 'Menos de 100 EUR')}</option>
                        <option value="100_300">100 - 300 EUR</option>
                        <option value="mas_300">{tr('Más de 300 EUR', 'Máis de 300 EUR')}</option>
                    </select>{error('budget_range')}</div>}
                    <div><label htmlFor="preferred_contact_method">{tr('¿Cómo prefieres que contactemos?', 'Como prefires que contactemos?')} *</label><select id="preferred_contact_method" className={inputClass('preferred_contact_method')} value={form.data.preferred_contact_method} onChange={(e) => form.setData('preferred_contact_method', e.target.value)}>
                        <option value="llamada">{tr('Llamada', 'Chamada')}</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="indiferente">{tr('Me da igual', 'Éme indiferente')}</option>
                    </select>{error('preferred_contact_method')}</div>
                    <div><label htmlFor="preferred_call_time">{tr('Horario preferido', 'Horario preferido')} *</label><select id="preferred_call_time" className={inputClass('preferred_call_time')} value={form.data.preferred_call_time} onChange={(e) => form.setData('preferred_call_time', e.target.value)}>
                        <option value="manana">{tr('Mañana', 'Mañá')}</option>
                        <option value="mediodia">{tr('Mediodía', 'Mediodía')}</option>
                        <option value="tarde">{tr('Tarde', 'Tarde')}</option>
                        <option value="indiferente">{tr('Me da igual', 'Éme indiferente')}</option>
                    </select>{error('preferred_call_time')}</div>
                    {form.data.type === 'advertiser' && selectedScreens.length > 0 && <p className="md:col-span-2 text-sm text-zinc-600">{tr('Pantallas seleccionadas', 'Pantallas seleccionadas')}: {selectedScreens.map((screen) => screen.name).join(', ')}</p>}
                    {form.data.type === 'advertiser' && <div className="md:col-span-2"><ScreenGrid screens={screens} selectable selected={form.data.selected_screen_ids} onToggle={toggleScreen} /></div>}
                    <div className="md:col-span-2"><label htmlFor="message">{tr('Mensaje', 'Mensaxe')} {form.data.type === 'other' ? '*' : ''}</label><textarea id="message" className={inputClass('message')} placeholder={tr('Cuéntanos cualquier detalle que debamos conocer', 'Cóntanos calquera detalle que debamos coñecer')} value={form.data.message} onChange={(e) => form.setData('message', e.target.value)} />{error('message')}</div>
                    {turnstileSiteKey && <div className="md:col-span-2"><TurnstileWidget siteKey={turnstileSiteKey} onVerify={(token) => form.setData('cf_turnstile_response', token)} />{error('cf_turnstile_response')}</div>}
                    <div className="md:col-span-2"><label className={`check ${errors.privacy_accepted ? 'input-error' : ''}`}><input type="checkbox" checked={form.data.privacy_accepted} onChange={(e) => form.setData('privacy_accepted', e.target.checked)} /> <span>{tr('Acepto la', 'Acepto a')} <Link className="font-semibold text-sky-700 underline" href="/privacidad">{tr('política de privacidad', 'política de privacidade')}</Link> {tr('y el tratamiento de mis datos para que Elixe pueda contactar conmigo.', 'e o tratamento dos meus datos para que Elixe poida contactar comigo.')}</span></label>{error('privacy_accepted')}</div>
                    <button className="btn-primary btn-large md:col-span-2" disabled={form.processing}><Send className="h-4 w-4" /> {form.processing ? tr('Enviando…', 'Enviando…') : tr('Enviar solicitud', 'Enviar solicitude')}</button>
                </form>
                </div>
            </section>
        </Layout>
    );
}

function Thanks() {
    const tr = useTranslation();

    return <Layout><Seo title={tr('Solicitud recibida', 'Solicitude recibida')} description={tr('El equipo de Elixe ha recibido tu solicitud.', 'O equipo de Elixe recibiu a túa solicitude.')} /><section className="success-page"><div className="success-card"><span className="success-icon"><ShieldCheck className="h-10 w-10" /></span><span className="eyebrow mt-6">{tr('Todo listo', 'Todo listo')}</span><h1 className="mt-4">{tr('Solicitud recibida', 'Solicitude recibida')}</h1><p className="mx-auto mt-4 max-w-md">{tr('El equipo de Elixe revisará la información y contactará contigo muy pronto.', 'O equipo de Elixe revisará a información e contactará contigo moi pronto.')}</p><Link className="btn-primary mt-8" href="/">{tr('Volver al inicio', 'Volver ao inicio')} <ArrowRight className="h-4 w-4" /></Link></div></section></Layout>;
}

function LegalPage({ title, page }: { title?: string; page?: Legal }) {
    const tr = useTranslation();
    const pageTitle = page?.title || title || 'Información legal';

    return <Layout><Seo title={pageTitle} description={`${pageTitle} de Elixe.`} /><section className="page-hero page-hero-compact"><div className="section py-14 md:py-20"><span className="eyebrow eyebrow-light"><ShieldCheck className="h-4 w-4" /> {tr('Información legal', 'Información legal')}</span><h1 className="mt-5 text-white">{pageTitle}</h1></div></section><section className="section section-roomy"><article className="legal-card"><p className="whitespace-pre-wrap">{page?.content || tr('Documento pendiente de configurar desde admin.', 'Documento pendente de configurar desde administración.')}</p></article></section></Layout>;
}

function AdminLogin() {
    const form = useForm({ email: '', password: '', remember: false });

    return (
        <div className="grid min-h-screen place-items-center bg-slate-100 px-5">
            <form className="form-panel w-full max-w-md" onSubmit={(event) => { event.preventDefault(); form.post('/admin/login'); }}>
                <div className="md:col-span-2">
                    <h1 className="text-3xl">Admin Elixe</h1>
                    <p>Acceso interno para gestionar leads, pantallas y sincronizacion.</p>
                </div>
                <input className="input md:col-span-2" type="email" placeholder="Email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} />
                {form.errors.email && <p className="md:col-span-2 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{form.errors.email}</p>}
                <input className="input md:col-span-2" type="password" placeholder="Password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} />
                {form.errors.password && <p className="md:col-span-2 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{form.errors.password}</p>}
                <label className="check md:col-span-2"><input type="checkbox" checked={form.data.remember} onChange={(e) => form.setData('remember', e.target.checked)} /> Recordarme</label>
                <button className="btn-primary md:col-span-2" disabled={form.processing}>Entrar</button>
            </form>
        </div>
    );
}

function AdminDashboard({ metrics, lastSync, recentLeads = [] }: { metrics: Record<string, number>; lastSync?: { status: string; started_at?: string; startedAt?: string } | null; recentLeads?: { id: number; name: string; type: string; status: string; createdAt?: string }[] }) {
    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>Dashboard</h1><p>Resumen operativo del MVP comercial.</p></div>
            </div>
            <div className="grid gap-4 md:grid-cols-3">
                <Stat label="leads nuevos" value={metrics.newLeads || 0} />
                <Stat label="leads semana" value={metrics.weekLeads || 0} />
                <Stat label="leads pendientes" value={metrics.pendingLeads || 0} />
                <Stat label="pantallas visibles" value={metrics.visibleScreens || 0} />
                <Stat label="pantallas incompletas" value={metrics.incompleteScreens || 0} />
                <Stat label="locales" value={metrics.venueLeads || 0} />
                <Stat label="anunciantes" value={metrics.advertiserLeads || 0} />
            </div>
            <div className="panel mt-6">
                <h2>Ultima sincronizacion</h2>
                <p>{lastSync ? `${lastSync.status} - ${lastSync.startedAt || lastSync.started_at || ''}` : 'Sin sincronizaciones registradas.'}</p>
            </div>
            <div className="panel mt-6"><div className="flex items-center justify-between gap-3"><h2>Solicitudes recientes</h2><Link className="btn-secondary py-2" href="/admin/leads">Ver bandeja</Link></div><div className="mt-4 grid gap-3">{recentLeads.map((lead) => <Link key={lead.id} href={`/admin/leads/${lead.id}`} className="flex items-center justify-between rounded-lg border border-slate-200 p-3 hover:border-sky-300"><span><strong>{lead.name}</strong><small className="block text-slate-500">{lead.type} · {lead.createdAt}</small></span><Badge tone={lead.status === 'nuevo' || lead.status === 'new' ? 'amber' : 'slate'}>{lead.status}</Badge></Link>)}{recentLeads.length === 0 && <p className="text-sm text-slate-500">Todavía no hay solicitudes.</p>}</div></div>
        </AdminLayout>
    );
}

function LeadStatusForm({ lead, statuses }: { lead: AdminLead; statuses: string[] }) {
    const form = useForm({ status: lead.status });

    return (
        <form className="flex gap-2" onSubmit={(event) => { event.preventDefault(); form.patch(`/admin/leads/${lead.id}/status`); }}>
            <select className="input min-h-9 py-1" value={form.data.status} onChange={(event) => form.setData('status', event.target.value)}>
                {statuses.map((status) => <option key={status} value={status}>{status}</option>)}
            </select>
            <button className="btn-secondary py-1" disabled={form.processing}>Guardar</button>
        </form>
    );
}

function AdminLeads({ leads, statuses, filters = {}, filterOptions = { provinces: [], municipalities: [], budgets: [], screens: [] } }: { leads: Paginator<AdminLead>; statuses: string[]; filters?: Record<string, string>; filterOptions?: { provinces: string[]; municipalities: string[]; budgets: string[]; screens: { id: number; display_name: string }[] } }) {
    const form = useForm({
        q: filters.q || '',
        type: filters.type || '',
        status: filters.status || '',
        from: filters.from || '',
        to: filters.to || '',
        province: filters.province || '',
        municipality: filters.municipality || '',
        budget: filters.budget || '',
        contact_method: filters.contact_method || '',
        screen_id: filters.screen_id || '',
    });
    const exportQuery = new URLSearchParams(Object.entries(form.data).filter(([, value]) => value)).toString();

    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>Leads</h1><p>Gestiona contactos, estados, reenvios y exportacion comercial.</p></div>
                <a className="btn-secondary" href={`/admin/leads/export${exportQuery ? `?${exportQuery}` : ''}`}>Exportar CSV</a>
            </div>
            <form className="panel mb-5 grid gap-3 md:grid-cols-6" onSubmit={(event) => { event.preventDefault(); form.get('/admin/leads', { preserveState: true }); }}>
                <label className="md:col-span-2"><span className="sr-only">Buscar</span><input className="input" type="search" placeholder="Nombre, email o teléfono" value={form.data.q} onChange={(event) => form.setData('q', event.target.value)} /></label>
                <label><span className="sr-only">Tipo</span><select className="input" value={form.data.type} onChange={(event) => form.setData('type', event.target.value)}><option value="">Todos los tipos</option><option value="venue">Local</option><option value="advertiser">Anunciante</option><option value="other">Consulta</option></select></label>
                <label><span className="sr-only">Estado</span><select className="input" value={form.data.status} onChange={(event) => form.setData('status', event.target.value)}><option value="">Todos los estados</option>{statuses.map((status) => <option key={status} value={status}>{status}</option>)}</select></label>
                <label><span className="text-xs text-slate-500">Desde</span><input className="input" type="date" value={form.data.from} onChange={(event) => form.setData('from', event.target.value)} /></label>
                <label><span className="text-xs text-slate-500">Hasta</span><input className="input" type="date" value={form.data.to} onChange={(event) => form.setData('to', event.target.value)} /></label>
                <details className="md:col-span-6"><summary className="cursor-pointer text-sm font-semibold text-sky-800">Más filtros</summary><div className="mt-3 grid gap-3 md:grid-cols-5"><select className="input" aria-label="Provincia" value={form.data.province} onChange={(event) => form.setData('province', event.target.value)}><option value="">Todas las provincias</option>{filterOptions.provinces.map((province) => <option key={province} value={province}>{province}</option>)}</select><select className="input" aria-label="Municipio" value={form.data.municipality} onChange={(event) => form.setData('municipality', event.target.value)}><option value="">Todos los municipios</option>{filterOptions.municipalities.map((municipality) => <option key={municipality} value={municipality}>{municipality}</option>)}</select><select className="input" aria-label="Presupuesto" value={form.data.budget} onChange={(event) => form.setData('budget', event.target.value)}><option value="">Cualquier presupuesto</option>{filterOptions.budgets.map((budget) => <option key={budget} value={budget}>{budget}</option>)}</select><select className="input" aria-label="Contacto preferido" value={form.data.contact_method} onChange={(event) => form.setData('contact_method', event.target.value)}><option value="">Cualquier contacto</option><option value="llamada">Llamada</option><option value="email">Email</option><option value="whatsapp">WhatsApp</option><option value="indiferente">Indiferente</option></select><select className="input" aria-label="Pantalla seleccionada" value={form.data.screen_id} onChange={(event) => form.setData('screen_id', event.target.value)}><option value="">Cualquier pantalla</option>{filterOptions.screens.map((screen) => <option key={screen.id} value={screen.id}>{screen.display_name}</option>)}</select></div></details>
                <div className="flex gap-2 md:col-span-6"><button className="btn-primary" disabled={form.processing}>Filtrar</button><Link className="btn-secondary" href="/admin/leads">Limpiar</Link></div>
            </form>
            <div className="admin-table">
                <table>
                    <thead><tr><th>Lead</th><th>Tipo</th><th>Estado</th><th>Contacto</th><th>Última acción</th><th>Fecha</th><th /></tr></thead>
                    <tbody>
                        {leads.data.map((lead) => (
                            <tr key={lead.id}>
                                <td><Link className="font-semibold text-sky-800" href={`/admin/leads/${lead.id}`}>{lead.name}</Link><span>{[lead.municipality, lead.province].filter(Boolean).join(', ')}</span></td>
                                <td><Badge tone={lead.type === 'advertiser' ? 'blue' : 'green'}>{lead.type}</Badge></td>
                                <td><LeadStatusForm lead={lead} statuses={statuses} /></td>
                                <td><span>{lead.contactName}</span><a className="text-sky-700" href={`mailto:${lead.email}`}>{lead.email}</a>{lead.phone && <a className="text-sky-700" href={`tel:${lead.phone}`}>{lead.phone}</a>}</td>
                                <td>{lead.lastAction || 'Sin acciones'}</td>
                                <td>{lead.createdAt}</td>
                                <td><Link className="btn-secondary py-2" href={`/admin/leads/${lead.id}`}>Ver</Link></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <Pagination page={leads} />
        </AdminLayout>
    );
}

function AdminLeadDetail({ lead, statuses, templates = [] }: { lead: AdminLead; statuses: string[]; templates?: ResponseTemplate[] }) {
    const resend = useForm({});
    const response = useForm({ response_template_id: templates[0]?.id || '' as number | string });
    const phone = lead.phone?.replace(/\D/g, '');
    const [copied, setCopied] = useState('');
    const copy = async (value: string, label: string) => {
        try {
            await navigator.clipboard.writeText(value);
            setCopied(label);
        } catch {
            setCopied('error');
        }
        window.setTimeout(() => setCopied(''), 1800);
    };

    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>{lead.name}</h1><p>{lead.contactName} · {lead.email} · {lead.phone}</p></div>
                <div className="flex flex-wrap gap-2"><button className="btn-secondary" type="button" onClick={() => copy(lead.email, 'email')}>{copied === 'email' ? 'Email copiado' : 'Copiar email'}</button>{lead.phone && <button className="btn-secondary" type="button" onClick={() => copy(lead.phone as string, 'phone')}>{copied === 'phone' ? 'Teléfono copiado' : 'Copiar teléfono'}</button>}<a className="btn-secondary" href={`mailto:${lead.email}`}>Email</a>{lead.phone && <a className="btn-secondary" href={`tel:${lead.phone}`}>Llamar</a>}{phone && <a className="btn-secondary" href={`https://wa.me/34${phone.replace(/^34/, '')}`} target="_blank" rel="noreferrer">WhatsApp</a>}</div>
            </div>
            {copied === 'error' && <p className="mb-4 text-sm text-red-700" role="alert">No se pudo copiar. Usa el enlace de contacto.</p>}
            <div className="grid gap-6 lg:grid-cols-[.7fr_1.3fr]">
                <div className="panel">
                    <h2>Estado</h2>
                    <div className="mt-4"><LeadStatusForm lead={lead} statuses={statuses} /></div>
                    <div className="mt-4 grid gap-2 text-sm text-slate-600">
                        <span>Tipo: {lead.type}</span>
                        <span>Contacto preferido: {lead.preferredContactMethod || '-'}</span>
                        <span>Horario: {lead.preferredCallTime || '-'}</span>
                        <span>Idioma: {lead.locale.toUpperCase()}</span>
                        <span>Creado: {lead.createdAt}</span>
                    </div>
                    <form className="mt-5 border-t border-slate-200 pt-5" onSubmit={(event) => { event.preventDefault(); resend.post(`/admin/leads/${lead.id}/resend`); }}><button className="btn-secondary w-full" disabled={resend.processing}>Reenviar email interno</button></form>
                </div>
                <div className="panel">
                    <h2>Detalle</h2>
                    <dl className="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                        <div><dt className="text-slate-500">Sector</dt><dd>{lead.activitySector || '-'}</dd></div>
                        <div><dt className="text-slate-500">Zona de interés</dt><dd>{lead.interestZone || '-'}</dd></div>
                        <div><dt className="text-slate-500">Presupuesto</dt><dd>{lead.budgetRange || '-'}</dd></div>
                        <div><dt className="text-slate-500">Tipo de local</dt><dd>{lead.locationType || '-'}</dd></div>
                    </dl>
                    <p className="mt-4 whitespace-pre-wrap">{lead.message || 'Sin mensaje.'}</p>
                    {lead.screens.length > 0 && <p className="mt-4">Pantallas seleccionadas: {lead.screens.join(', ')}</p>}
                </div>
            </div>
            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <div className="panel"><h2>Enviar respuesta</h2>{templates.length > 0 ? <form className="mt-4 grid gap-3" onSubmit={(event) => { event.preventDefault(); response.post(`/admin/leads/${lead.id}/response`); }}><label><span className="text-sm text-slate-600">Plantilla</span><select className="input mt-1" value={response.data.response_template_id} onChange={(event) => response.setData('response_template_id', Number(event.target.value))}>{templates.map((template) => <option key={template.id} value={template.id}>{template.name} · {template.subject}</option>)}</select></label>{response.errors.response_template_id && <p className="field-error">{response.errors.response_template_id}</p>}<button className="btn-primary" disabled={response.processing}>Enviar al contacto</button></form> : <p className="mt-3 text-sm">No hay plantillas activas compatibles. Configúralas en Plantillas de email.</p>}</div>
                <div className="panel"><h2>Historial</h2><ol className="mt-4 grid gap-4">{lead.activities.map((activity) => <li key={activity.id} className="border-l-2 border-sky-200 pl-4"><strong className="text-sm">{activity.description}</strong><p className="text-xs text-slate-500">{activity.user} · {activity.createdAt}</p></li>)}{lead.activities.length === 0 && <li className="text-sm text-slate-500">Sin actividad registrada.</li>}</ol></div>
            </div>
        </AdminLayout>
    );
}

function ScreenVisibilityForm({ screen }: { screen: AdminScreen }) {
    const form = useForm({});
    const hidden = screen.localVisibilityOverride === false;

    return (
        <form onSubmit={(event) => {
            event.preventDefault();
            if (window.confirm(hidden ? '¿Mostrar esta pantalla en la web si Xibo lo permite?' : '¿Ocultar esta pantalla de la web publica?')) {
                form.patch(`/admin/screens/${screen.id}/${hidden ? 'show' : 'hide'}`);
            }
        }}>
            <button className="btn-secondary py-2" disabled={form.processing}>{hidden ? 'Mostrar' : 'Ocultar'}</button>
        </form>
    );
}

function AdminScreens({ screens, filters = {} }: { screens: Paginator<AdminScreen>; filters?: { status?: string } }) {
    const sync = useForm({});
    const filter = useForm({ status: filters.status || '' });

    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>Pantallas</h1><p>Datos sincronizados desde Xibo. Corrige campos principales en Xibo y vuelve a sincronizar.</p></div>
                <form onSubmit={(event) => {
                    event.preventDefault();
                    if (window.confirm('¿Sincronizar pantallas desde Xibo ahora?')) {
                        sync.post('/admin/screens/sync');
                    }
                }}>
                    <button className="btn-primary" disabled={sync.processing}>Sincronizar ahora</button>
                </form>
            </div>
            <form className="panel mb-5 flex flex-wrap items-end gap-3" onSubmit={(event) => { event.preventDefault(); filter.get('/admin/screens', { preserveState: true }); }}><label><span className="text-xs text-slate-500">Estado comercial</span><select className="input mt-1 min-w-48" value={filter.data.status} onChange={(event) => filter.setData('status', event.target.value)}><option value="">Todos</option><option value="disponible">Disponible</option><option value="retirada">Retirada</option></select></label><button className="btn-secondary" disabled={filter.processing}>Filtrar</button>{filter.data.status && <Link className="btn-secondary" href="/admin/screens">Limpiar</Link>}</form>
            <div className="admin-table">
                <table>
                    <thead><tr><th>Pantalla</th><th>Ubicacion</th><th>Tipo</th><th>Visible</th><th>Estado</th><th>Avisos</th><th /></tr></thead>
                    <tbody>
                        {screens.data.map((screen) => (
                            <tr key={screen.id}>
                                <td><strong>{screen.internalName}</strong><span>{screen.publicName}</span><span>{screen.syncedAt}</span></td>
                                <td>{[screen.municipality, screen.province].filter(Boolean).join(', ') || '-'}</td>
                                <td><span>{screen.locationType || '-'}</span><span>{screen.locationSector || '-'}</span></td>
                                <td><Badge tone={screen.isVisiblePublicly ? 'green' : 'slate'}>{screen.isVisiblePublicly ? 'visible' : 'oculta'}</Badge></td>
                                <td><span>{screen.commercialStatus || '-'}</span><span>{screen.online ? 'online' : 'offline'}</span></td>
                                <td>
                                    {screen.visibilityBlockers.length > 0 ? <Badge tone="amber">{screen.visibilityBlockers[0]}</Badge> : <Badge tone="green">OK</Badge>}
                                    {screen.missingFields.length > 0 && <span>Campos: {screen.missingFields.join(', ')}</span>}
                                    {screen.visibilityBlockers.slice(1).map((reason) => <span key={reason}>{reason}</span>)}
                                    <span>{screen.warning}</span>
                                </td>
                                <td><ScreenVisibilityForm screen={screen} /></td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <Pagination page={screens} />
        </AdminLayout>
    );
}

function AdminSyncRuns({ runs }: { runs: Paginator<any> }) {
    return (
        <AdminLayout>
            <div className="section-head"><div><h1>Sync logs</h1><p>Historial de sincronizacion con Xibo.</p></div></div>
            <div className="admin-table">
                <table>
                    <thead><tr><th>Inicio</th><th>Estado</th><th>Registros</th><th>Error</th></tr></thead>
                    <tbody>
                        {runs.data.map((run) => (
                            <tr key={run.id}>
                                <td><span>{run.startedAt}</span><span>{run.finishedAt}</span></td>
                                <td><Badge tone={run.status === 'success' ? 'green' : run.status === 'failed' ? 'red' : 'amber'}>{run.status}</Badge></td>
                                <td>{run.recordsFound} encontrados · {run.recordsCreated} creados · {run.recordsUpdated} actualizados · {run.recordsSkipped} omitidos</td>
                                <td>{run.errorMessage || '-'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <Pagination page={runs} />
        </AdminLayout>
    );
}

function AdminDiagnostics({ runs, latest }: { runs: any[]; latest?: any | null }) {
    const form = useForm({});

    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>Diagnosticos</h1><p>Comprueba Xibo, entorno y por que una pantalla no aparece en el mapa publico.</p></div>
                <form onSubmit={(event) => { event.preventDefault(); form.post('/admin/diagnostics/run'); }}>
                    <button className="btn-primary" disabled={form.processing}>Ejecutar diagnostico</button>
                </form>
            </div>
            {latest && (
                <div className="grid gap-4">
                    {Object.entries(latest.checks).map(([name, check]: [string, any]) => (
                        <article className="panel" key={name}>
                            <div className="flex items-center justify-between gap-3">
                                <h2>{name}</h2>
                                <Badge tone={check.status === 'success' ? 'green' : check.status === 'failed' ? 'red' : 'amber'}>{check.status}</Badge>
                            </div>
                            <p className="mt-2">{check.message}</p>
                            {check.details?.issues?.length > 0 && <ul className="mt-4 list-disc space-y-1 pl-5 text-sm text-amber-800">{check.details.issues.map((issue: string) => <li key={issue}>{issue}</li>)}</ul>}
                            {check.details?.blocked_screens?.length > 0 && (
                                <div className="mt-4 grid gap-3">
                                    {check.details.blocked_screens.map((screen: any) => (
                                        <div className="rounded border border-amber-200 bg-amber-50 p-3 text-sm" key={screen.id}>
                                            <strong>{screen.name}</strong>
                                            <p>{screen.public_name || ''}</p>
                                            <ul className="mt-2 list-disc pl-5 text-amber-900">
                                                {(screen.blockers.length ? screen.blockers : screen.missing_fields).map((item: string) => <li key={item}>{item}</li>)}
                                            </ul>
                                        </div>
                                    ))}
                                </div>
                            )}
                            {check.details && !check.details.blocked_screens && <pre className="mt-4 overflow-auto rounded bg-slate-950 p-3 text-xs text-white">{JSON.stringify(check.details, null, 2)}</pre>}
                        </article>
                    ))}
                </div>
            )}
            <div className="panel mt-6">
                <h2>Historial</h2>
                <div className="admin-table mt-4">
                    <table>
                        <thead><tr><th>Fecha</th><th>Estado</th><th>Checks</th></tr></thead>
                        <tbody>{runs.map((run) => <tr key={run.id}><td>{run.startedAt}</td><td><Badge tone={run.status === 'success' ? 'green' : run.status === 'failed' ? 'red' : 'amber'}>{run.status}</Badge></td><td>{Object.keys(run.checks).join(', ')}</td></tr>)}</tbody>
                    </table>
                </div>
            </div>
        </AdminLayout>
    );
}

function ContentBlockForm({ block }: { block: ContentBlock }) {
    const form = useForm({
        title_es: block.title_es || '',
        title_gl: block.title_gl || '',
        subtitle_es: block.subtitle_es || '',
        subtitle_gl: block.subtitle_gl || '',
        content_es: block.content_es || '',
        content_gl: block.content_gl || '',
        active: block.active,
    });

    return (
        <form className="panel grid gap-3 md:grid-cols-2" onSubmit={(event) => { event.preventDefault(); form.patch(`/admin/content/${block.id}`); }}>
            <div className="md:col-span-2 flex items-center justify-between"><h3>{block.key}</h3><label className="check"><input type="checkbox" checked={form.data.active} onChange={(e) => form.setData('active', e.target.checked)} /> Activo</label></div>
            <input className="input" placeholder="Titulo ES" value={form.data.title_es} onChange={(e) => form.setData('title_es', e.target.value)} />
            <input className="input" placeholder="Titulo GL" value={form.data.title_gl} onChange={(e) => form.setData('title_gl', e.target.value)} />
            <textarea className="input" placeholder="Subtitulo ES" value={form.data.subtitle_es} onChange={(e) => form.setData('subtitle_es', e.target.value)} />
            <textarea className="input" placeholder="Subtitulo GL" value={form.data.subtitle_gl} onChange={(e) => form.setData('subtitle_gl', e.target.value)} />
            <textarea className="input" placeholder="Contenido ES" value={form.data.content_es} onChange={(e) => form.setData('content_es', e.target.value)} />
            <textarea className="input" placeholder="Contenido GL" value={form.data.content_gl} onChange={(e) => form.setData('content_gl', e.target.value)} />
            <button className="btn-primary md:col-span-2" disabled={form.processing}>Guardar bloque</button>
        </form>
    );
}

function AdminContentBlocks({ blocks }: { blocks: ContentBlock[] }) {
    return <AdminLayout><div className="section-head"><div><h1>Contenido</h1><p>Bloques editables ES/GL para la landing.</p></div></div><div className="grid gap-5">{blocks.map((block) => <ContentBlockForm key={block.id} block={block} />)}</div></AdminLayout>;
}

function FaqForm({ faq }: { faq?: Faq }) {
    const form = useForm({
        category: faq?.category || 'general',
        question_es: faq?.question_es || '',
        question_gl: faq?.question_gl || '',
        answer_es: faq?.answer_es || '',
        answer_gl: faq?.answer_gl || '',
        active: faq?.active ?? true,
        sort_order: faq?.sort_order || 0,
    });
    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        if (faq) {
            form.patch(`/admin/faqs/${faq.id}`);
        } else {
            form.post('/admin/faqs');
        }
    };

    return (
        <form className="panel grid gap-3 md:grid-cols-2" onSubmit={submit}>
            <select className="input" value={form.data.category} onChange={(e) => form.setData('category', e.target.value)}>
                <option value="general">General</option>
                <option value="locales">Locales</option>
                <option value="anunciantes">Anunciantes</option>
            </select>
            <input className="input" type="number" min="0" value={form.data.sort_order} onChange={(e) => form.setData('sort_order', Number(e.target.value))} />
            <input className="input" placeholder="Pregunta ES" value={form.data.question_es} onChange={(e) => form.setData('question_es', e.target.value)} />
            <input className="input" placeholder="Pregunta GL" value={form.data.question_gl} onChange={(e) => form.setData('question_gl', e.target.value)} />
            <textarea className="input" placeholder="Respuesta ES" value={form.data.answer_es} onChange={(e) => form.setData('answer_es', e.target.value)} />
            <textarea className="input" placeholder="Respuesta GL" value={form.data.answer_gl} onChange={(e) => form.setData('answer_gl', e.target.value)} />
            <label className="check"><input type="checkbox" checked={form.data.active} onChange={(e) => form.setData('active', e.target.checked)} /> Activa</label>
            <button className="btn-primary" disabled={form.processing}>{faq ? 'Guardar FAQ' : 'Crear FAQ'}</button>
        </form>
    );
}

function AdminFaqs({ faqs }: { faqs: Faq[] }) {
    return <AdminLayout><div className="section-head"><div><h1>FAQs</h1><p>Preguntas frecuentes por categoria, con ES/GL.</p></div></div><div className="grid gap-5"><FaqForm />{faqs.map((faq) => <FaqForm key={faq.id} faq={faq} />)}</div></AdminLayout>;
}

function LegalPageForm({ page }: { page: Legal }) {
    const form = useForm({
        title_es: page.title_es || '',
        title_gl: page.title_gl || '',
        content_es: page.content_es || '',
        content_gl: page.content_gl || '',
        active: page.active ?? true,
    });

    return (
        <form className="panel grid gap-3 md:grid-cols-2" onSubmit={(event) => { event.preventDefault(); form.patch(`/admin/legal-pages/${page.id}`); }}>
            <div className="md:col-span-2 flex items-center justify-between"><h3>{page.slug}</h3><label className="check"><input type="checkbox" checked={form.data.active} onChange={(e) => form.setData('active', e.target.checked)} /> Activa</label></div>
            <input className="input" placeholder="Titulo ES" value={form.data.title_es} onChange={(e) => form.setData('title_es', e.target.value)} />
            <input className="input" placeholder="Titulo GL" value={form.data.title_gl} onChange={(e) => form.setData('title_gl', e.target.value)} />
            <textarea className="input" placeholder="Contenido ES" value={form.data.content_es} onChange={(e) => form.setData('content_es', e.target.value)} />
            <textarea className="input" placeholder="Contenido GL" value={form.data.content_gl} onChange={(e) => form.setData('content_gl', e.target.value)} />
            <button className="btn-primary md:col-span-2" disabled={form.processing}>Guardar texto legal</button>
        </form>
    );
}

function AdminLegalPages({ pages }: { pages: Legal[] }) {
    return <AdminLayout><div className="section-head"><div><h1>Textos legales</h1><p>Privacidad, cookies y aviso legal editables.</p></div></div><div className="grid gap-5">{pages.map((page) => <LegalPageForm key={page.id} page={page} />)}</div></AdminLayout>;
}

function ResponseTemplateForm({ template }: { template: ResponseTemplate }) {
    const form = useForm({
        key: template.key,
        name: template.name,
        lead_type: template.lead_type || '',
        locale: template.locale,
        subject: template.subject,
        body: template.body || '',
        is_active: template.is_active ?? true,
    });

    return <form className="panel grid gap-3" onSubmit={(event) => { event.preventDefault(); form.patch(`/admin/response-templates/${template.id}`); }}><div className="grid gap-3 md:grid-cols-4"><input className="input bg-slate-100" aria-label="Clave" value={form.data.key} readOnly /><input className="input md:col-span-2" aria-label="Nombre" value={form.data.name} onChange={(event) => form.setData('name', event.target.value)} /><select className="input" aria-label="Idioma" value={form.data.locale} onChange={(event) => form.setData('locale', event.target.value)}><option value="es">Español</option><option value="gl">Gallego</option></select></div><div className="grid gap-3 md:grid-cols-[1fr_3fr]"><select className="input" aria-label="Tipo de lead" value={form.data.lead_type} onChange={(event) => form.setData('lead_type', event.target.value)}><option value="">Cualquier tipo</option><option value="venue">Local</option><option value="advertiser">Anunciante</option><option value="other">Consulta</option></select><input className="input" aria-label="Asunto" value={form.data.subject} onChange={(event) => form.setData('subject', event.target.value)} /></div><textarea className="input min-h-40" aria-label="Cuerpo" value={form.data.body} onChange={(event) => form.setData('body', event.target.value)} /><p className="text-xs text-slate-500">Variables: {'{{contact_name}}'}, {'{{business_name}}'}, {'{{email}}'}, {'{{phone}}'}, {'{{lead_type}}'}</p><div className="flex items-center justify-between gap-3"><label className="check"><input type="checkbox" checked={form.data.is_active} onChange={(event) => form.setData('is_active', event.target.checked)} /> Activa</label><button className="btn-primary" disabled={form.processing}>Guardar plantilla</button></div>{Object.keys(form.errors).length > 0 && <p className="field-error">Revisa los campos de la plantilla.</p>}</form>;
}

function AdminResponseTemplates({ templates }: { templates: ResponseTemplate[] }) {
    const create = useForm({ key: '', name: '', lead_type: '', locale: 'es', subject: '', body: '', is_active: true });

    return <AdminLayout><div className="section-head"><div><h1>Plantillas de email</h1><p>Edita confirmaciones automáticas y respuestas comerciales sin desplegar código.</p></div></div><details className="panel mb-5"><summary className="cursor-pointer font-semibold">Crear plantilla</summary><form className="mt-4 grid gap-3" onSubmit={(event) => { event.preventDefault(); create.post('/admin/response-templates', { onSuccess: () => create.reset() }); }}><div className="grid gap-3 md:grid-cols-3"><input className="input" placeholder="clave_unica" value={create.data.key} onChange={(event) => create.setData('key', event.target.value)} /><input className="input" placeholder="Nombre interno" value={create.data.name} onChange={(event) => create.setData('name', event.target.value)} /><select className="input" value={create.data.locale} onChange={(event) => create.setData('locale', event.target.value)}><option value="es">Español</option><option value="gl">Gallego</option></select></div><input className="input" placeholder="Asunto" value={create.data.subject} onChange={(event) => create.setData('subject', event.target.value)} /><textarea className="input min-h-32" placeholder="Cuerpo del mensaje" value={create.data.body} onChange={(event) => create.setData('body', event.target.value)} /><button className="btn-primary" disabled={create.processing}>Crear</button></form></details><div className="grid gap-5">{templates.map((template) => <ResponseTemplateForm key={template.id} template={template} />)}</div></AdminLayout>;
}

function SettingForm({ setting }: { setting: Setting }) {
    const form = useForm({ value: setting.value || '' });

    return (
        <form className="panel grid gap-3 md:grid-cols-[1fr_2fr_auto]" onSubmit={(event) => { event.preventDefault(); form.patch(`/admin/settings/${setting.id}`); }}>
            <div><h3>{setting.label || setting.key}</h3><p className="text-xs">{setting.key}{setting.is_public ? ' · publico' : ''}</p></div>
            <input className="input" type={setting.type === 'email' ? 'email' : 'text'} value={form.data.value} onChange={(e) => form.setData('value', e.target.value)} />
            <button className="btn-primary" disabled={form.processing}>Guardar</button>
        </form>
    );
}

function AdminSettings({ settings }: { settings: Setting[] }) {
    return <AdminLayout><div className="section-head"><div><h1>Configuracion</h1><p>Contacto publico y email receptor de leads.</p></div></div><div className="grid gap-4">{settings.map((setting) => <SettingForm key={setting.id} setting={setting} />)}</div></AdminLayout>;
}

const pages: Record<string, React.ComponentType<any>> = {
    Home,
    'Screens/Index': ScreensPage,
    'Venues/Index': VenuesPage,
    'Advertisers/Index': AdvertisersPage,
    Advice: AdvicePage,
    Thanks,
    'Legal/Privacy': () => <LegalPage title="Politica de privacidad" />,
    'Legal/Cookies': () => <LegalPage title="Politica de cookies" />,
    'Legal/Notice': () => <LegalPage title="Aviso legal" />,
    'Legal/Page': LegalPage,
    'Admin/Login': AdminLogin,
    'Admin/Dashboard': AdminDashboard,
    'Admin/Leads': AdminLeads,
    'Admin/LeadDetail': AdminLeadDetail,
    'Admin/Screens': AdminScreens,
    'Admin/SyncRuns': AdminSyncRuns,
    'Admin/ContentBlocks': AdminContentBlocks,
    'Admin/Faqs': AdminFaqs,
    'Admin/LegalPages': AdminLegalPages,
    'Admin/ResponseTemplates': AdminResponseTemplates,
    'Admin/Settings': AdminSettings,
    'Admin/Diagnostics': AdminDiagnostics,
};

createInertiaApp({
    resolve: (name) => pages[name],
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
