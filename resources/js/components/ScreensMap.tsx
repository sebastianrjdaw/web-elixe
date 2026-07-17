import L from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { useEffect } from 'react';
import { MapContainer, Marker, Popup, TileLayer, useMap } from 'react-leaflet';

type Screen = {
    id: string;
    name: string;
    municipality?: string;
    province?: string;
    latitude?: number;
    longitude?: number;
    locationType?: string;
    locationSector?: string;
};

delete (L.Icon.Default.prototype as { _getIconUrl?: unknown })._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

function FitMapBounds({ screens }: { screens: Screen[] }) {
    const map = useMap();

    useEffect(() => {
        const coordinates = screens.map((screen) => [screen.latitude as number, screen.longitude as number] as [number, number]);

        if (coordinates.length === 1) {
            map.setView(coordinates[0], 13);
        } else if (coordinates.length > 1) {
            map.fitBounds(coordinates, { padding: [35, 35], maxZoom: 13 });
        }
    }, [map, screens]);

    return null;
}

export default function ScreensMap({ screens }: { screens: Screen[] }) {
    const validScreens = screens.filter((screen) => Number.isFinite(screen.latitude) && Number.isFinite(screen.longitude));
    const center: [number, number] = validScreens.length > 0
        ? [validScreens[0].latitude as number, validScreens[0].longitude as number]
        : [42.8782, -8.5448];

    return (
        <MapContainer center={center} zoom={validScreens.length ? 13 : 8} scrollWheelZoom={false} className="map-frame">
            <FitMapBounds screens={validScreens} />
            <TileLayer attribution="&copy; OpenStreetMap contributors" url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
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
