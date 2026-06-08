/**
 * Timeline Data Processor (Node.js / Edge Function)
 * Transforms raw service JSON into a simplified timeline format.
 */

const processTimeline = (rawData) => {
  if (!rawData || !rawData.pendencias) return [];

  return rawData.pendencias.map(p => {
    const createdAt = new Date(p.created_at);
    const updatedAt = new Date(p.updated_at);
    const diffTime = Math.abs(updatedAt - createdAt);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    let stakeholder = "Castro"; // Default
    let color = "#3c8dbc"; // Default Blue (Castro)

    if (p.responsavel_tipo === "cliente") {
      stakeholder = "Cliente";
      color = "#00a65a"; // Green
    }

    if (p.responsavel_tipo === "op") {
      stakeholder = "Órgão Público";
      color = "#f39c12"; // Orange
    }

    const keywords = ["órgão", "prefeitura", "semmas", "danc"];
    const content = (p.pendencia + " " + (p.observacoes || "")).toLowerCase();
    
    if (keywords.some(kw => content.includes(kw))) {
      stakeholder = "Órgão Público";
      color = "#f39c12"; // Orange
    }

    return {
      etapa: p.pendencia,
      stakeholder: stakeholder,
      dias: diffDays,
      cor: color,
      observacoes: p.observacoes || "",
      status: p.status === "concluido" ? "Concluído" : "Em andamento",
      data_conclusao: p.status === "concluido" ? updatedAt.toLocaleDateString('pt-BR') : null
    };
  });
};

// Example usage context (for Edge Function compatibility)
/*
export default async function handler(req, res) {
  const { service } = req.body;
  const result = processTimeline(service);
  res.status(200).json(result);
}
*/

// For local testing/reference
module.exports = { processTimeline };
