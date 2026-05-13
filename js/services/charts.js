/**
 * Lightweight line chart (canvas)
 */
export function drawLineChart(canvas, series, options = {}) {
  const ctx = canvas.getContext("2d");
  if (!ctx) return;
  const { padding = 24, lineColor = "#3b82f6", fill = true } = options;
  const w = canvas.width;
  const h = canvas.height;
  ctx.clearRect(0, 0, w, h);
  const max = Math.max(...series, 1);
  const min = 0;
  const innerW = w - padding * 2;
  const innerH = h - padding * 2;
  const step = series.length > 1 ? innerW / (series.length - 1) : innerW;
  const pts = series.map((v, i) => ({
    x: padding + i * step,
    y: padding + innerH - ((v - min) / (max - min)) * innerH,
  }));
  ctx.beginPath();
  ctx.moveTo(pts[0].x, pts[0].y);
  for (let i = 1; i < pts.length; i++) ctx.lineTo(pts[i].x, pts[i].y);
  ctx.strokeStyle = lineColor;
  ctx.lineWidth = 2.5;
  ctx.lineJoin = "round";
  ctx.stroke();
  if (fill && pts.length) {
    ctx.lineTo(pts[pts.length - 1].x, padding + innerH);
    ctx.lineTo(pts[0].x, padding + innerH);
    ctx.closePath();
    const g = ctx.createLinearGradient(0, padding, 0, padding + innerH);
    g.addColorStop(0, "rgba(59,130,246,0.25)");
    g.addColorStop(1, "rgba(59,130,246,0)");
    ctx.fillStyle = g;
    ctx.fill();
  }
}

export function drawBarChart(canvas, values, options = {}) {
  const ctx = canvas.getContext("2d");
  if (!ctx) return;
  const { labels = [], color = "#3b82f6" } = options;
  const w = canvas.width;
  const h = canvas.height;
  ctx.clearRect(0, 0, w, h);
  const pad = 28;
  const max = Math.max(...values, 1);
  const barW = (w - pad * 2) / values.length - 8;
  values.forEach((v, i) => {
    const x = pad + i * ((w - pad * 2) / values.length) + 4;
    const bh = ((h - pad * 2) * v) / max;
    const y = h - pad - bh;
    const grd = ctx.createLinearGradient(0, y, 0, h - pad);
    grd.addColorStop(0, color);
    grd.addColorStop(1, "#1d4ed8");
    ctx.fillStyle = grd;
    ctx.fillRect(x, y, barW, bh);
    if (labels[i]) {
      ctx.fillStyle = "#64748b";
      ctx.font = "11px Inter, sans-serif";
      ctx.textAlign = "center";
      ctx.fillText(labels[i], x + barW / 2, h - 8);
    }
  });
}
