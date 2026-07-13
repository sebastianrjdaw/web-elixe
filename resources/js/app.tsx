import '../css/app.css';
import 'leaflet/dist/leaflet.css';
import './bootstrap';
import { createInertiaApp, Link, useForm } from '@inertiajs/react';
import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { createRoot } from 'react-dom/client';
import { ArrowRight, Building2, Check, MapPin, Monitor, Moon, Send, ShieldCheck, Store, Sun } from 'lucide-react';
import React, { PropsWithChildren, useEffect, useMemo, useState } from 'react';
import { MapContainer, Marker, Popup, TileLayer } from 'react-leaflet';

delete (L.Icon.Default.prototype as any)._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

type Screen = {
    id: number;
    name: string;
    municipality?: string;
    province?: string;
    latitude?: number;
    longitude?: number;
    locationType?: string;
    locationSector?: string;
    commercialStatus?: string;
};

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
    preferredContactMethod?: string;
    preferredCallTime?: string;
    screens: string[];
    createdAt?: string;
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

function Layout({ children }: PropsWithChildren) {
    const [dark, setDark] = useState(false);

    return (
        <div className={dark ? 'dark min-h-screen bg-slate-950 text-white' : 'min-h-screen bg-sky-50 text-zinc-950'}>
            <header className="border-b border-zinc-200 bg-white/95">
                <nav className="mx-auto flex max-w-6xl items-center justify-between px-5 py-4">
                    <Link href="/" className="text-lg font-semibold tracking-tight">Elixe</Link>
                    <div className="flex items-center gap-1 text-sm">
                        <Link className="nav-link" href="/">Inicio</Link>
                        <Link className="nav-link" href="/locales">Para locales</Link>
                        <Link className="nav-link" href="/anunciantes">Para anunciantes</Link>
                        <Link className="nav-link" href="/red-de-pantallas">Red de pantallas</Link>
                        <Link className="nav-link" href="/asesoramiento">Solicitar asesoramiento</Link>
                        <button type="button" className="icon-btn" onClick={() => setDark((value) => !value)} aria-label="Cambiar modo">
                            {dark ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
                        </button>
                    </div>
                </nav>
            </header>
            <main>{children}</main>
            <footer className="border-t border-zinc-200 bg-white">
                <div className="mx-auto flex max-w-6xl flex-col gap-3 px-5 py-8 text-sm text-zinc-600 sm:flex-row sm:items-center sm:justify-between">
                    <span>Elixe Ads Platform</span>
                    <div className="flex gap-4">
                        <Link href="/privacidad">Privacidad</Link>
                        <Link href="/cookies">Cookies</Link>
                        <Link href="/aviso-legal">Aviso legal</Link>
                    </div>
                </div>
            </footer>
        </div>
    );
}

function AdminLayout({ children }: PropsWithChildren) {
    const logout = useForm({});

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
                    <Link className="admin-nav" href="/admin/settings">Configuracion</Link>
                    <Link className="admin-nav" href="/admin/diagnostics">Diagnosticos</Link>
                </nav>
            </aside>
            <div className="lg:pl-64">
                <header className="flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
                    <div className="flex gap-2 text-sm lg:hidden">
                        <Link className="admin-nav" href="/admin/leads">Leads</Link>
                        <Link className="admin-nav" href="/admin/screens">Pantallas</Link>
                    </div>
                    <Link className="text-sm text-slate-600" href="/">Ver web</Link>
                    <form onSubmit={(event) => { event.preventDefault(); logout.post('/admin/logout'); }}>
                        <button className="btn-secondary py-2" type="submit">Salir</button>
                    </form>
                </header>
                <main className="mx-auto max-w-6xl px-5 py-8">{children}</main>
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

function Pagination<T>({ page }: { page: Paginator<T> }) {
    if (!page.links?.length) {
        return null;
    }

    return (
        <div className="mt-6 flex flex-wrap gap-2">
            {page.links.map((link, index) => link.url ? (
                <Link key={index} href={link.url} className={`rounded border px-3 py-2 text-sm ${link.active ? 'border-sky-700 bg-sky-700 text-white' : 'border-slate-200 bg-white text-slate-700'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
            ) : (
                <span key={index} className="rounded border border-slate-200 px-3 py-2 text-sm text-slate-400" dangerouslySetInnerHTML={{ __html: link.label }} />
            ))}
        </div>
    );
}

function Stat({ label, value }: { label: string; value: number }) {
    return <div className="metric"><strong>{value}</strong><span>{label}</span></div>;
}

function ScreenGrid({ screens, selectable = false, selected = [], onToggle }: { screens: Screen[]; selectable?: boolean; selected?: number[]; onToggle?: (id: number) => void }) {
    return (
        <div className="grid gap-4 md:grid-cols-2">
            {screens.map((screen) => (
                <button type="button" key={screen.id} onClick={() => selectable && onToggle?.(screen.id)} className={`screen-card text-left ${selected.includes(screen.id) ? 'ring-2 ring-emerald-600' : ''}`}>
                    <div className="flex items-start justify-between gap-3">
                        <div>
                            <h3>{screen.name}</h3>
                        </div>
                        {selectable && selected.includes(screen.id) ? <Check className="h-5 w-5 text-emerald-700" /> : <Monitor className="h-5 w-5 text-zinc-500" />}
                    </div>
                    <p className="mt-3 flex items-center gap-2 text-sm text-zinc-600"><MapPin className="h-4 w-4" />{[screen.municipality, screen.province].filter(Boolean).join(', ') || 'Ubicacion aproximada'}</p>
                    <div className="mt-4 flex flex-wrap gap-2 text-xs">
                        <span className="pill">{screen.locationType || 'local'}</span>
                        <span className="pill">{screen.locationSector || 'sector'}</span>
                        <span className="pill">{screen.commercialStatus || 'disponible'}</span>
                    </div>
                </button>
            ))}
            {screens.length === 0 && <p className="rounded-md border border-zinc-200 bg-white p-5 text-sm text-zinc-600">Todavia no hay pantallas publicas sincronizadas.</p>}
        </div>
    );
}

function ScreensMap({ screens }: { screens: Screen[] }) {
    const [mounted, setMounted] = useState(false);
    const validScreens = screens.filter((screen) => Number.isFinite(screen.latitude) && Number.isFinite(screen.longitude));
    const center: [number, number] = validScreens.length && validScreens[0].latitude && validScreens[0].longitude
        ? [validScreens[0].latitude, validScreens[0].longitude]
        : [42.8782, -8.5448];

    useEffect(() => {
        setMounted(true);
    }, []);

    if (!mounted) {
        return <div className="h-[420px] w-full rounded-2xl border border-zinc-200 bg-white" />;
    }

    return (
        <MapContainer
            center={center}
            zoom={validScreens.length ? 13 : 8}
            scrollWheelZoom={false}
            className="h-[420px] w-full rounded-2xl border border-zinc-200"
        >
            <TileLayer
                attribution="&copy; OpenStreetMap contributors"
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            {validScreens.map((screen) => (
                <Marker key={screen.id} position={[screen.latitude as number, screen.longitude as number]}>
                    <Popup>
                        <div className="space-y-1">
                            <strong>{screen.name}</strong>
                            <div>{screen.locationType || 'Local'} · {screen.locationSector || 'Sector local'}</div>
                            <div>{[screen.municipality, screen.province].filter(Boolean).join(', ')}</div>
                        </div>
                    </Popup>
                </Marker>
            ))}
        </MapContainer>
    );
}

function Home({ summary, screens = [], contentBlocks = {}, faqs = [] }: { summary: { totalScreens: number; activeScreens: number; availableScreens: number }; screens?: Screen[]; contentBlocks?: Record<string, ContentBlock>; faqs?: Faq[] }) {
    const hero = contentBlocks.hero || {};
    const venues = contentBlocks.venues || {};
    const advertisers = contentBlocks.advertisers || {};
    const how = contentBlocks.how_it_works || {};

    return (
        <Layout>
            <section className="hero">
                <div className="mx-auto grid max-w-6xl gap-10 px-5 py-16 md:grid-cols-[1.1fr_.9fr] md:items-center">
                    <div>
                        <h1>{hero.title || 'Publicidad local en pantallas reales.'}</h1>
                        <p>{hero.subtitle || 'Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.'}</p>
                        <div className="mt-8 flex flex-wrap gap-3">
                            <Link className="btn-primary" href="/asesoramiento"><Store className="h-4 w-4" /> Solicitar asesoramiento</Link>
                            <Link className="btn-secondary" href="/red-de-pantallas"><Building2 className="h-4 w-4" /> Ver red de pantallas</Link>
                        </div>
                    </div>
                    <div className="panel">
                        <div className="grid grid-cols-3 gap-3">
                            <Stat label="pantallas" value={summary.totalScreens} />
                            <Stat label="activas" value={summary.activeScreens} />
                            <Stat label="disponibles" value={summary.availableScreens} />
                        </div>
                        <div className="mt-5"><ScreensMap screens={screens} /></div>
                    </div>
                </div>
            </section>
            <section className="section grid-gap">
                <article><h2>{venues.title || 'Para locales'}</h2><p>{venues.content || 'Tu pantalla tambien puede mostrar contenido propio del local: promociones, menus, avisos o eventos.'}</p></article>
                <article><h2>{advertisers.title || 'Para anunciantes'}</h2><p>{advertisers.content || 'Llega a clientes locales y amplia la visibilidad de tu negocio en pantallas reales.'}</p></article>
                <article><h2>{how.title || 'Como funciona'}</h2><p>{how.content || 'Recogemos tu solicitud, revisamos el encaje y preparamos una propuesta personalizada.'}</p></article>
            </section>
            {faqs.length > 0 && (
                <section className="section">
                    <div className="section-head"><div><h2>Preguntas frecuentes</h2></div></div>
                    <div className="grid gap-4 md:grid-cols-2">
                        {faqs.map((faq) => (
                            <article key={faq.id} className="panel">
                                <h3>{faq.question}</h3>
                                <p className="mt-3">{faq.answer}</p>
                            </article>
                        ))}
                    </div>
                </section>
            )}
        </Layout>
    );
}

function ScreensPage({ screens }: { screens: Screen[] }) {
    const [sector, setSector] = useState('');
    const sectors = useMemo(() => [...new Set(screens.map((screen) => screen.locationSector).filter(Boolean))], [screens]);
    const filtered = sector ? screens.filter((screen) => screen.locationSector === sector) : screens;
    const selectScreen = (id: number) => {
        sessionStorage.setItem('elixe.selectedScreens', JSON.stringify([id]));
        window.location.href = '/asesoramiento?tipo=advertiser';
    };

    return (
        <Layout>
            <section className="section">
                <div className="section-head">
                    <div><h1>Pantallas disponibles</h1><p>Consulta las pantallas disponibles para campañas locales en Galicia.</p></div>
                    <select className="input max-w-xs" value={sector} onChange={(event) => setSector(event.target.value)}>
                        <option value="">Todos los sectores</option>
                        {sectors.map((item) => <option key={item} value={item}>{item}</option>)}
                    </select>
                </div>
                <ScreensMap screens={filtered} />
                <div className="mt-6"><ScreenGrid screens={filtered} selectable selected={[]} onToggle={selectScreen} /></div>
            </section>
        </Layout>
    );
}

function VenuesPage() {
    const form = useForm({ business_name: '', contact_name: '', email: '', phone: '', address: '', city: '', province: '', location_type: '', has_tv: false, wants_elixe_screen: false, wants_ad_revenue: true, wants_ad_control: false, message: '', privacy_accepted: false });

    return (
        <Layout>
            <section className="section form-layout">
                <div><h1>Locales y establecimientos</h1><p>Cuentanos si ya tienes television, si necesitas una solucion de pantalla y que tipo de publicidad quieres aceptar.</p></div>
                <form className="form-panel" onSubmit={(event) => { event.preventDefault(); form.post('/locales/solicitud'); }}>
                    <input className="input" placeholder="Nombre del local" value={form.data.business_name} onChange={(e) => form.setData('business_name', e.target.value)} />
                    <input className="input" placeholder="Persona de contacto" value={form.data.contact_name} onChange={(e) => form.setData('contact_name', e.target.value)} />
                    <input className="input" placeholder="Email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} />
                    <input className="input" placeholder="Telefono" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} />
                    <input className="input md:col-span-2" placeholder="Direccion" value={form.data.address} onChange={(e) => form.setData('address', e.target.value)} />
                    <input className="input" placeholder="Ciudad" value={form.data.city} onChange={(e) => form.setData('city', e.target.value)} />
                    <input className="input" placeholder="Provincia" value={form.data.province} onChange={(e) => form.setData('province', e.target.value)} />
                    <input className="input md:col-span-2" placeholder="Tipo de local" value={form.data.location_type} onChange={(e) => form.setData('location_type', e.target.value)} />
                    <label className="check"><input type="checkbox" checked={form.data.has_tv} onChange={(e) => form.setData('has_tv', e.target.checked)} /> Ya tengo television</label>
                    <label className="check"><input type="checkbox" checked={form.data.wants_elixe_screen} onChange={(e) => form.setData('wants_elixe_screen', e.target.checked)} /> Quiero pantalla Elixe</label>
                    <label className="check"><input type="checkbox" checked={form.data.wants_ad_revenue} onChange={(e) => form.setData('wants_ad_revenue', e.target.checked)} /> Quiero generar ingresos</label>
                    <label className="check"><input type="checkbox" checked={form.data.wants_ad_control} onChange={(e) => form.setData('wants_ad_control', e.target.checked)} /> Quiero controlar publicidad</label>
                    <textarea className="input md:col-span-2" placeholder="Comentarios" value={form.data.message} onChange={(e) => form.setData('message', e.target.value)} />
                    <label className="check md:col-span-2"><input type="checkbox" checked={form.data.privacy_accepted} onChange={(e) => form.setData('privacy_accepted', e.target.checked)} /> Acepto la politica de privacidad</label>
                    <button className="btn-primary md:col-span-2" disabled={form.processing}><Send className="h-4 w-4" /> Enviar solicitud</button>
                </form>
            </section>
        </Layout>
    );
}

function AdvertisersPage({ screens }: { screens: Screen[] }) {
    const form = useForm({ company_name: '', contact_name: '', email: '', phone: '', sector: '', campaign_message: '', preferred_dates: '', budget_range: '', selected_screen_ids: [] as number[], selected_zones: [] as string[], message: '', privacy_accepted: false });
    const toggle = (id: number) => form.setData('selected_screen_ids', form.data.selected_screen_ids.includes(id) ? form.data.selected_screen_ids.filter((value) => value !== id) : [...form.data.selected_screen_ids, id]);

    return (
        <Layout>
            <section className="section">
                <div className="section-head"><div><h1>Anunciantes</h1><p>Selecciona pantallas de interes y solicita una llamada con el equipo comercial.</p></div></div>
                <div className="grid gap-8 lg:grid-cols-[1fr_1fr]">
                    <ScreenGrid screens={screens} selectable selected={form.data.selected_screen_ids} onToggle={toggle} />
                    <form className="form-panel" onSubmit={(event) => { event.preventDefault(); form.post('/anunciantes/solicitud'); }}>
                        <input className="input" placeholder="Empresa" value={form.data.company_name} onChange={(e) => form.setData('company_name', e.target.value)} />
                        <input className="input" placeholder="Contacto" value={form.data.contact_name} onChange={(e) => form.setData('contact_name', e.target.value)} />
                        <input className="input" placeholder="Email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} />
                        <input className="input" placeholder="Telefono" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} />
                        <input className="input md:col-span-2" placeholder="Sector" value={form.data.sector} onChange={(e) => form.setData('sector', e.target.value)} />
                        <textarea className="input md:col-span-2" placeholder="Mensaje publicitario" value={form.data.campaign_message} onChange={(e) => form.setData('campaign_message', e.target.value)} />
                        <input className="input" placeholder="Fechas aproximadas" value={form.data.preferred_dates} onChange={(e) => form.setData('preferred_dates', e.target.value)} />
                        <input className="input" placeholder="Presupuesto orientativo" value={form.data.budget_range} onChange={(e) => form.setData('budget_range', e.target.value)} />
                        <textarea className="input md:col-span-2" placeholder="Comentarios" value={form.data.message} onChange={(e) => form.setData('message', e.target.value)} />
                        <label className="check md:col-span-2"><input type="checkbox" checked={form.data.privacy_accepted} onChange={(e) => form.setData('privacy_accepted', e.target.checked)} /> Acepto la politica de privacidad</label>
                        <button className="btn-primary md:col-span-2" disabled={form.processing}><ArrowRight className="h-4 w-4" /> Solicitar llamada</button>
                    </form>
                </div>
            </section>
        </Layout>
    );
}

function AdvicePage({ screens, turnstileSiteKey }: { screens: Screen[]; turnstileSiteKey?: string | null }) {
    const params = new URLSearchParams(window.location.search);
    const initialType = (params.get('tipo') === 'advertiser' ? 'advertiser' : 'venue') as 'venue' | 'advertiser' | 'other';
    const form = useForm({
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
        selected_screen_ids: [] as number[],
        preferred_contact_method: 'llamada',
        preferred_call_time: 'indiferente',
        message: '',
        privacy_accepted: false,
        cf_turnstile_response: '',
    });

    useEffect(() => {
        const stored = sessionStorage.getItem('elixe.selectedScreens');
        if (!stored) {
            return;
        }

        try {
            const selected = JSON.parse(stored);
            if (Array.isArray(selected)) {
                form.setData('selected_screen_ids', selected.map(Number).filter(Boolean));
                form.setData('type', 'advertiser');
            }
        } catch {
            sessionStorage.removeItem('elixe.selectedScreens');
        }
    }, []);

    const selectedScreens = screens.filter((screen) => form.data.selected_screen_ids.includes(screen.id));
    const toggleScreen = (id: number) => form.setData('selected_screen_ids', form.data.selected_screen_ids.includes(id) ? form.data.selected_screen_ids.filter((value) => value !== id) : [...form.data.selected_screen_ids, id]);
    const errors = form.errors as Record<string, string>;
    const error = (field: string) => errors[field] ? <p className="field-error">{errors[field]}</p> : null;
    const inputClass = (field: string) => `input ${errors[field] ? 'input-error' : ''}`;
    const validateClient = () => {
        const nextErrors: Record<string, string> = {};
        const required = (field: keyof typeof form.data, label: string) => {
            const value = form.data[field];
            if (value === null || value === undefined || value === '' || value === false) {
                nextErrors[field] = `El campo ${label} es obligatorio.`;
            }
        };

        if (form.data.type === 'venue') {
            required('business_name', 'nombre del local');
            required('province', 'provincia');
            required('municipality', 'municipio');
            required('location_type', 'tipo de local');
        }

        if (form.data.type === 'advertiser') {
            required('company_name', 'nombre de empresa');
            required('activity_sector', 'sector de actividad');
            required('interest_zone', 'zona de interes');
            required('budget_range', 'presupuesto orientativo');
        }

        if (form.data.type === 'other') {
            required('message', 'mensaje');
        }

        required('contact_name', 'nombre de contacto');
        required('phone', 'telefono');
        required('email', 'email');
        required('preferred_contact_method', 'preferencia de contacto');
        required('preferred_call_time', 'horario preferido');
        required('privacy_accepted', 'politica de privacidad');

        if (form.data.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.data.email)) {
            nextErrors.email = 'Introduce un email valido.';
        }

        if (form.data.phone && !/^(\+34|0034)?[6789]\d{8}$/.test(form.data.phone)) {
            nextErrors.phone = 'Introduce un telefono espanol valido.';
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
            <section className="section">
                <div className="section-head">
                    <div><h1>Solicitar asesoramiento</h1><p>Cuéntanos qué necesitas y el equipo de Elixe revisará tu solicitud para preparar el siguiente paso.</p></div>
                </div>
                <div className="grid gap-4 md:grid-cols-3">
                    <button type="button" className={`choice ${form.data.type === 'venue' ? 'choice-active' : ''}`} onClick={() => { form.setData('type', 'venue'); form.clearErrors(); }}>
                        <Store className="h-5 w-5" />Tengo un local
                    </button>
                    <button type="button" className={`choice ${form.data.type === 'advertiser' ? 'choice-active' : ''}`} onClick={() => { form.setData('type', 'advertiser'); form.clearErrors(); }}>
                        <Building2 className="h-5 w-5" />Quiero anunciarme
                    </button>
                    <button type="button" className={`choice ${form.data.type === 'other' ? 'choice-active' : ''}`} onClick={() => { form.setData('type', 'other'); form.clearErrors(); }}>
                        <Send className="h-5 w-5" />Tengo otra consulta
                    </button>
                </div>
                <form className="form-panel mt-8" noValidate onSubmit={(event) => { event.preventDefault(); if (validateClient()) form.post('/asesoramiento'); }}>
                    {form.data.type === 'venue' && <div><input className={inputClass('business_name')} placeholder="Nombre del local *" value={form.data.business_name} onChange={(e) => form.setData('business_name', e.target.value)} />{error('business_name')}</div>}
                    {form.data.type === 'advertiser' && <div><input className={inputClass('company_name')} placeholder="Nombre de empresa *" value={form.data.company_name} onChange={(e) => form.setData('company_name', e.target.value)} />{error('company_name')}</div>}
                    <div><input className={inputClass('contact_name')} placeholder="Nombre de contacto *" value={form.data.contact_name} onChange={(e) => form.setData('contact_name', e.target.value)} />{error('contact_name')}</div>
                    <div><input className={inputClass('phone')} placeholder="Telefono *" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} />{error('phone')}</div>
                    <div><input className={inputClass('email')} placeholder="Email *" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} />{error('email')}</div>
                    <div><input className={inputClass('province')} placeholder={form.data.type === 'venue' ? 'Provincia *' : 'Provincia'} value={form.data.province} onChange={(e) => form.setData('province', e.target.value)} />{error('province')}</div>
                    <div><input className={inputClass('municipality')} placeholder={form.data.type === 'venue' ? 'Municipio *' : 'Municipio'} value={form.data.municipality} onChange={(e) => form.setData('municipality', e.target.value)} />{error('municipality')}</div>
                    {form.data.type === 'venue' && <div><select className={inputClass('location_type')} value={form.data.location_type} onChange={(e) => form.setData('location_type', e.target.value)}>
                        <option value="">Tipo de local</option>
                        {['bar', 'restaurante', 'cafeteria', 'lavanderia', 'gimnasio', 'peluqueria', 'clinica', 'tienda', 'hotel', 'supermercado', 'oficina', 'centro_comercial', 'farmacia', 'autoescuela', 'estanco', 'panaderia', 'coworking', 'otro'].map((type) => <option key={type} value={type}>{type}</option>)}
                    </select>{error('location_type')}</div>}
                    {form.data.type === 'venue' && <label className="check"><input type="checkbox" checked={form.data.has_screen} onChange={(e) => form.setData('has_screen', e.target.checked)} /> Tiene pantalla actualmente</label>}
                    {form.data.type === 'venue' && <label className="check"><input type="checkbox" checked={form.data.wants_elixe_screen} onChange={(e) => form.setData('wants_elixe_screen', e.target.checked)} /> Quiere que Elixe proporcione pantalla</label>}
                    {form.data.type === 'venue' && <label className="check"><input type="checkbox" checked={form.data.wants_ad_control} onChange={(e) => form.setData('wants_ad_control', e.target.checked)} /> Quiere controlar publicidad</label>}
                    {form.data.type === 'advertiser' && <div><select className={inputClass('activity_sector')} value={form.data.activity_sector} onChange={(e) => form.setData('activity_sector', e.target.value)}>
                        <option value="">Sector de actividad</option>
                        {['Hosteleria', 'Comercio local', 'Salud y bienestar', 'Servicios profesionales', 'Inmobiliaria', 'Automocion', 'Educacion', 'Eventos', 'Turismo', 'Ocio', 'Otro'].map((sector) => <option key={sector} value={sector}>{sector}</option>)}
                    </select>{error('activity_sector')}</div>}
                    {form.data.type === 'advertiser' && <div><input className={inputClass('interest_zone')} placeholder="Zona de interes *" value={form.data.interest_zone} onChange={(e) => form.setData('interest_zone', e.target.value)} />{error('interest_zone')}</div>}
                    {form.data.type === 'advertiser' && <div><select className={inputClass('budget_range')} value={form.data.budget_range} onChange={(e) => form.setData('budget_range', e.target.value)}>
                        <option value="">Presupuesto orientativo</option>
                        <option value="menos_100">Menos de 100 EUR</option>
                        <option value="100_300">100 - 300 EUR</option>
                        <option value="mas_300">Mas de 300 EUR</option>
                    </select>{error('budget_range')}</div>}
                    <div><select className={inputClass('preferred_contact_method')} value={form.data.preferred_contact_method} onChange={(e) => form.setData('preferred_contact_method', e.target.value)}>
                        <option value="llamada">Llamada</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="indiferente">Me da igual</option>
                    </select>{error('preferred_contact_method')}</div>
                    <div><select className={inputClass('preferred_call_time')} value={form.data.preferred_call_time} onChange={(e) => form.setData('preferred_call_time', e.target.value)}>
                        <option value="manana">Mañana</option>
                        <option value="mediodia">Mediodia</option>
                        <option value="tarde">Tarde</option>
                        <option value="indiferente">Me da igual</option>
                    </select>{error('preferred_call_time')}</div>
                    {form.data.type === 'advertiser' && selectedScreens.length > 0 && <p className="md:col-span-2 text-sm text-zinc-600">Pantallas seleccionadas: {selectedScreens.map((screen) => screen.name).join(', ')}</p>}
                    {form.data.type === 'advertiser' && <div className="md:col-span-2"><ScreenGrid screens={screens} selectable selected={form.data.selected_screen_ids} onToggle={toggleScreen} /></div>}
                    <div className="md:col-span-2"><textarea className={inputClass('message')} placeholder={form.data.type === 'other' ? 'Mensaje libre *' : 'Mensaje libre'} value={form.data.message} onChange={(e) => form.setData('message', e.target.value)} />{error('message')}</div>
                    {turnstileSiteKey && <div className="md:col-span-2"><input className={inputClass('cf_turnstile_response')} placeholder="Token Turnstile" value={form.data.cf_turnstile_response} onChange={(e) => form.setData('cf_turnstile_response', e.target.value)} />{error('cf_turnstile_response')}</div>}
                    <div className="md:col-span-2"><label className={`check ${errors.privacy_accepted ? 'input-error' : ''}`}><input type="checkbox" checked={form.data.privacy_accepted} onChange={(e) => form.setData('privacy_accepted', e.target.checked)} /> Acepto la politica de privacidad y el tratamiento de mis datos para que Elixe pueda contactar conmigo.</label>{error('privacy_accepted')}</div>
                    <button className="btn-primary md:col-span-2" disabled={form.processing}><Send className="h-4 w-4" /> Enviar solicitud</button>
                </form>
            </section>
        </Layout>
    );
}

function Thanks() {
    return <Layout><section className="section narrow"><ShieldCheck className="h-10 w-10 text-emerald-700" /><h1>Solicitud recibida</h1><p>El equipo de Elixe revisara la informacion y contactara contigo.</p><Link className="btn-secondary mt-6" href="/">Volver al inicio</Link></section></Layout>;
}

function LegalPage({ title, page }: { title?: string; page?: Legal }) {
    return <Layout><section className="section narrow"><h1>{page?.title || title}</h1><p className="whitespace-pre-wrap">{page?.content || 'Documento pendiente de configurar desde admin.'}</p></section></Layout>;
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

function AdminDashboard({ metrics, lastSync }: { metrics: Record<string, number>; lastSync?: { status: string; started_at?: string; startedAt?: string } | null }) {
    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>Dashboard</h1><p>Resumen operativo del MVP comercial.</p></div>
            </div>
            <div className="grid gap-4 md:grid-cols-3">
                <Stat label="leads nuevos" value={metrics.newLeads || 0} />
                <Stat label="leads semana" value={metrics.weekLeads || 0} />
                <Stat label="pantallas visibles" value={metrics.visibleScreens || 0} />
                <Stat label="pantallas incompletas" value={metrics.incompleteScreens || 0} />
                <Stat label="locales" value={metrics.venueLeads || 0} />
                <Stat label="anunciantes" value={metrics.advertiserLeads || 0} />
            </div>
            <div className="panel mt-6">
                <h2>Ultima sincronizacion</h2>
                <p>{lastSync ? `${lastSync.status} - ${lastSync.startedAt || lastSync.started_at || ''}` : 'Sin sincronizaciones registradas.'}</p>
            </div>
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

function AdminLeads({ leads, statuses }: { leads: Paginator<AdminLead>; statuses: string[] }) {
    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>Leads</h1><p>Gestiona contactos, estados, reenvios y exportacion comercial.</p></div>
                <a className="btn-secondary" href="/admin/leads/export">Exportar CSV</a>
            </div>
            <div className="admin-table">
                <table>
                    <thead><tr><th>Lead</th><th>Tipo</th><th>Estado</th><th>Contacto</th><th>Fecha</th><th /></tr></thead>
                    <tbody>
                        {leads.data.map((lead) => (
                            <tr key={lead.id}>
                                <td><Link className="font-semibold text-sky-800" href={`/admin/leads/${lead.id}`}>{lead.name}</Link><span>{[lead.municipality, lead.province].filter(Boolean).join(', ')}</span></td>
                                <td><Badge tone={lead.type === 'advertiser' ? 'blue' : 'green'}>{lead.type}</Badge></td>
                                <td><LeadStatusForm lead={lead} statuses={statuses} /></td>
                                <td><span>{lead.contactName}</span><span>{lead.email}</span><span>{lead.phone}</span></td>
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

function AdminLeadDetail({ lead, statuses }: { lead: AdminLead; statuses: string[] }) {
    const resend = useForm({});

    return (
        <AdminLayout>
            <div className="section-head">
                <div><h1>{lead.name}</h1><p>{lead.contactName} · {lead.email} · {lead.phone}</p></div>
                <form onSubmit={(event) => { event.preventDefault(); resend.post(`/admin/leads/${lead.id}/resend`); }}>
                    <button className="btn-secondary" disabled={resend.processing}>Reenviar email interno</button>
                </form>
            </div>
            <div className="grid gap-6 lg:grid-cols-[.7fr_1.3fr]">
                <div className="panel">
                    <h2>Estado</h2>
                    <div className="mt-4"><LeadStatusForm lead={lead} statuses={statuses} /></div>
                    <div className="mt-4 grid gap-2 text-sm text-slate-600">
                        <span>Tipo: {lead.type}</span>
                        <span>Contacto preferido: {lead.preferredContactMethod || '-'}</span>
                        <span>Horario: {lead.preferredCallTime || '-'}</span>
                        <span>Creado: {lead.createdAt}</span>
                    </div>
                </div>
                <div className="panel">
                    <h2>Detalle</h2>
                    <p className="mt-4 whitespace-pre-wrap">{lead.message || 'Sin mensaje.'}</p>
                    {lead.screens.length > 0 && <p className="mt-4">Pantallas seleccionadas: {lead.screens.join(', ')}</p>}
                </div>
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

function AdminScreens({ screens }: { screens: Paginator<AdminScreen> }) {
    const sync = useForm({});

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
        faq ? form.patch(`/admin/faqs/${faq.id}`) : form.post('/admin/faqs');
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
    'Admin/Settings': AdminSettings,
    'Admin/Diagnostics': AdminDiagnostics,
};

createInertiaApp({
    resolve: (name) => pages[name],
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
