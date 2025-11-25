// src/pages/admin/VendorRequests.tsx
import { useEffect, useState } from "react";
import { apiGet, apiPost } from "@/lib/api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import BackButton from "@/components/BackButton";
import {
  Table, TableHeader, TableRow, TableHead, TableBody, TableCell,
} from "@/components/ui/table";
import { alertError, alertSuccess, confirm } from "@/lib/alerts";

type PerfilVendedor = {
  id_usuario: number;
  telefono?: string | null;
  zona?: string | null;
  brand?: string | null;
  company?: string | null;
  website?: string | null;
  message?: string | null;
};

type PendingVendor = {
  id: number;
  name: string;
  email: string;
  rol: string;                // "vendedor"
  vendor_status: "pending" | "approved" | "rejected";
  vendor_note?: string | null;
  perfil_vendedor?: PerfilVendedor | null;
};

export default function VendorRequests() {
  const [data, setData] = useState<PendingVendor[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("");
  const [rejectingId, setRejectingId] = useState<number | null>(null);
  const [rejectNotes, setRejectNotes] = useState("");

  async function load() {
    setLoading(true);
    try {
      const rows = await apiGet<PendingVendor[]>("/vendors/requests");
      setData(rows ?? []);
    } catch (e: any) {
      await alertError(e?.message || "No se pudieron cargar las solicitudes.");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => { load(); }, []);

  const approve = async (u: PendingVendor) => {
    const ok = await confirm("Aprobar solicitud", `¿Aprobar a “${u.name}”?`, "Aprobar");
    if (!ok) return;
    try {
      await apiPost(`/vendors/${u.id}/approve`, {});
      await alertSuccess("Vendedor aprobado. Se ha notificado por correo.");
      await load();
    } catch (e: any) {
      await alertError(e?.message || "No se pudo aprobar.");
    }
  };

  const openReject = (u: PendingVendor) => {
    setRejectingId(u.id);
    setRejectNotes("");
  };

  const doReject = async () => {
    if (rejectingId == null) return;
    try {
      await apiPost(`/vendors/${rejectingId}/reject`, { notes: rejectNotes });
      setRejectingId(null);
      setRejectNotes("");
      await alertSuccess("Solicitud rechazada. Se ha notificado por correo.");
      await load();
    } catch (e: any) {
      await alertError(e?.message || "No se pudo rechazar.");
    }
  };

  const rows = data.filter((u) => {
    if (!filter.trim()) return true;
    const f = filter.toLowerCase();
    const tel = u.perfil_vendedor?.telefono ?? "";
    const zona = u.perfil_vendedor?.zona ?? "";
    const brand = u.perfil_vendedor?.brand ?? "";
    const company = u.perfil_vendedor?.company ?? "";
    const website = u.perfil_vendedor?.website ?? "";
    const message = u.perfil_vendedor?.message ?? "";
    return (
      u.name.toLowerCase().includes(f) ||
      u.email.toLowerCase().includes(f) ||
      tel.toLowerCase().includes(f) ||
      zona.toLowerCase().includes(f) ||
      brand.toLowerCase().includes(f) ||
      company.toLowerCase().includes(f) ||
      website.toLowerCase().includes(f) ||
      message.toLowerCase().includes(f)
    );
  });

  return (
    <div className="container mx-auto px-4" style={{ marginTop: 96 }}>
      <div className="mb-4 flex items-center justify-between gap-3">
        <div className="flex items-center gap-3">
          <h1 className="text-2xl font-semibold">Solicitudes de vendedor</h1>
          <BackButton to="/admin" label="Volver al panel" />
        </div>
        <Input
          value={filter}
          onChange={(e) => setFilter(e.target.value)}
          placeholder="Buscar por nombre, email, teléfono o zona…"
          className="max-w-md"
        />
      </div>

      {loading ? (
        <div className="text-sm text-muted-foreground">Cargando…</div>
      ) : (
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <TableHead>Nombre</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Teléfono</TableHead>
              <TableHead>Zona</TableHead>
              <TableHead>Marca</TableHead>
              <TableHead>Empresa</TableHead>
              <TableHead>Web</TableHead>
              <TableHead>Mensaje</TableHead>
              <TableHead className="text-right">Acciones</TableHead>

            </TableRow>
          </TableHeader>
          <TableBody>
            {rows.map((u) => (
              <TableRow key={u.id}>
                <TableCell>{u.id}</TableCell>
                <TableCell>{u.name}</TableCell>
                <TableCell>{u.email}</TableCell>
                <TableCell>{u.perfil_vendedor?.telefono ?? "—"}</TableCell>
                <TableCell className="capitalize">{u.perfil_vendedor?.zona ?? "—"}</TableCell>
                <TableCell>{u.perfil_vendedor?.brand ?? "—"}</TableCell>
                <TableCell>{u.perfil_vendedor?.company ?? "—"}</TableCell>
                <TableCell>
                  {u.perfil_vendedor?.website ? (
                    <a
                      href={u.perfil_vendedor.website}
                      target="_blank"
                      rel="noreferrer"
                      className="text-primary underline"
                    >
                      Web
                    </a>
                  ) : (
                    "—"
                  )}
                </TableCell>
                <TableCell
                  className="max-w-[260px] truncate"
                  title={u.perfil_vendedor?.message ?? ""}
                >
                  {u.perfil_vendedor?.message ?? "—"}
                </TableCell>
                <TableCell className="text-right space-x-2">
                  <Button size="sm" onClick={() => approve(u)}>Aprobar</Button>
                  <Button size="sm" variant="destructive" onClick={() => openReject(u)}>Rechazar</Button>
                </TableCell>

              </TableRow>
            ))}
            {rows.length === 0 && (
              <TableRow>
                <TableCell colSpan={7} className="text-center text-sm text-muted-foreground">
                  No hay solicitudes pendientes.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      )}

      {/* Modal rechazar (simple, sin librería extra) */}
      {rejectingId !== null && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div className="w-full max-w-lg rounded-lg border bg-background p-4 shadow-lg">
            <div className="text-lg font-medium mb-2">Rechazar solicitud</div>
            <p className="text-sm text-muted-foreground mb-3">
              Puedes indicar un motivo (opcional). Se enviará en el correo.
            </p>
            <textarea
              value={rejectNotes}
              onChange={(e) => setRejectNotes(e.target.value)}
              rows={4}
              className="w-full rounded border bg-background p-2 text-sm"
              placeholder="Motivo del rechazo…"
            />
            <div className="mt-4 flex justify-end gap-2">
              <Button variant="outline" onClick={() => setRejectingId(null)}>Cancelar</Button>
              <Button variant="destructive" onClick={doReject}>Rechazar</Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
