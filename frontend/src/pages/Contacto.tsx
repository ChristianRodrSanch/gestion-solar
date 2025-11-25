import { useState } from "react";
import { Mail, MessageCircle } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { alertSuccess, alertError } from "@/lib/alerts";
import { apiPostJson } from "@/lib/http";
import Footer from "@/components/Footer";

export default function Contacto() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [message, setMessage] = useState("");
  const [sending, setSending] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (sending) return;

    setSending(true);
    try {
      await apiPostJson("/contact", { name, email, message });
      await alertSuccess(
        "Mensaje enviado",
        "Hemos recibido tu consulta. Te responderemos lo antes posible."
      );
      setName("");
      setEmail("");
      setMessage("");
    } catch (err: any) {
      await alertError(err?.message || "No se pudo enviar el mensaje");
    } finally {
      setSending(false);
    }
  };

  return (
    <div className="min-h-screen flex flex-col pt-20">
    <main className="min-h-screen bg-gradient-to-br from-background via-background to-background">
      <div className="container mx-auto px-4 pt-24 pb-16 grid gap-10 md:grid-cols-2 items-start">
        {/* Columna izquierda: texto */}
        <div className="space-y-4">
          <div className="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary">
            <MessageCircle className="h-4 w-4" />
            Estamos aquí para ayudarte
          </div>

          <h1 className="text-3xl md:text-4xl font-semibold">
            Contacta con el equipo de Enerflux
          </h1>

          <p className="text-muted-foreground leading-relaxed">
            Si tienes dudas sobre nuestros productos, tu instalación fotovoltaica,
            el estado de un pedido o simplemente quieres que te asesoremos antes
            de tomar una decisión, rellena el formulario y un administrador se
            pondrá en contacto contigo.
          </p>

          <p className="text-sm text-muted-foreground/80">
            Normalmente respondemos en menos de 24 horas en horario laboral.
          </p>
        </div>

        {/* Columna derecha: formulario */}
        <div className="bg-background/70 border border-border/60 rounded-xl p-6 shadow-xl backdrop-blur">
          <div className="flex items-center gap-2 mb-4">
            <Mail className="h-5 w-5 text-primary" />
            <h2 className="text-lg font-medium">Envíanos un mensaje</h2>
          </div>

          <form className="space-y-4" onSubmit={handleSubmit}>
            <div className="grid gap-2">
              <label htmlFor="contact-name" className="text-sm font-medium">
                Nombre
              </label>
              <Input
                id="contact-name"
                placeholder="Tu nombre"
                value={name}
                onChange={(e) => setName(e.target.value)}
                required
              />
            </div>

            <div className="grid gap-2">
              <label htmlFor="contact-email" className="text-sm font-medium">
                Email
              </label>
              <Input
                id="contact-email"
                type="email"
                placeholder="tucorreo@ejemplo.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>

            <div className="grid gap-2">
              <label htmlFor="contact-message" className="text-sm font-medium">
                Mensaje
              </label>
              <Textarea
                id="contact-message"
                placeholder="Cuéntanos en qué podemos ayudarte…"
                rows={5}
                value={message}
                onChange={(e) => setMessage(e.target.value)}
                required
              />
            </div>

            <Button
              type="submit"
              className="w-full"
              disabled={sending}
            >
              {sending ? "Enviando..." : "Enviar mensaje"}
            </Button>
          </form>
        </div>
      </div>
      
    </main>
    <Footer />
    </div>
      );
        
}
