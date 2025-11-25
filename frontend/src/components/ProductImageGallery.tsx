import { useMemo, useRef, useState } from "react";

type Props = {
  images: string[];           // URLs absolutas (http://... o /storage/...)
  aspect?: string;            // tailwind aspect ratio: "aspect-square" | "aspect-video" | etc
  className?: string;
  thumbSize?: number;         // px
};

export default function ProductImageGallery({
  images,
  aspect = "aspect-square",
  className = "",
  thumbSize = 72,
}: Props) {
  const safeImages = useMemo(
    () => (Array.isArray(images) ? images.filter(Boolean) : []).slice(0, 10),
    [images]
  );
  const [idx, setIdx] = useState(0);
  const mainUrl = safeImages[idx] || "";

  // Zoom al pasar el ratón
  const containerRef = useRef<HTMLDivElement>(null);
  const [bgPos, setBgPos] = useState("center");
  const [zooming, setZooming] = useState(false);

  const onMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!containerRef.current) return;
    const rect = containerRef.current.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    setBgPos(`${x}% ${y}%`);
  };

  return (
    <div className={`w-full ${className}`}>
      {/* Imagen principal con zoom */}
      <div
        ref={containerRef}
        className={`relative rounded-lg overflow-hidden border bg-muted/10 ${aspect}`}
        onMouseEnter={() => setZooming(true)}
        onMouseLeave={() => setZooming(false)}
        onMouseMove={onMove}
        style={{
          backgroundImage: `url("${mainUrl}")`,
          backgroundPosition: bgPos,
          backgroundRepeat: "no-repeat",
          backgroundSize: zooming ? "200%" : "cover",
          transition: "background-size 120ms ease",
        }}
        aria-label="Imagen principal del producto"
      >
        {/* capa para accesibilidad (imagen visible en móvil sin hover) */}
        <img
          src={mainUrl}
          alt="Producto"
          className="absolute inset-0 w-full h-full object-cover pointer-events-none opacity-0 sm:opacity-100 sm:mix-blend-multiply"
          style={{ filter: "opacity(0)" }}
        />
      </div>

      {/* Tira de miniaturas */}
      {safeImages.length > 1 && (
        <div className="mt-3 flex gap-2 overflow-x-auto pb-1">
          {safeImages.map((src, i) => (
            <button
              key={`${src}-${i}`}
              onClick={() => setIdx(i)}
              className={`shrink-0 rounded-md border overflow-hidden ${
                i === idx ? "ring-2 ring-primary" : "hover:opacity-80"
              }`}
              style={{ width: thumbSize, height: thumbSize }}
              title={`Imagen ${i + 1}`}
            >
              <img
                src={src}
                alt={`Miniatura ${i + 1}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
