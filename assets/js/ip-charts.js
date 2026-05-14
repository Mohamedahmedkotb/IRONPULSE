/**
 * Vanilla canvas line chart (no modules) — IronPulse dashboard.
 */
function ipDrawLineChart(canvas, series, options) {
  options = options || {};
  var padding = options.padding != null ? options.padding : 24;
  var lineColor = options.lineColor || "#3b82f6";
  var fill = options.fill !== false;
  var ctx = canvas.getContext("2d");
  if (!ctx) return;
  var w = canvas.width;
  var h = canvas.height;
  var max = Math.max.apply(null, series.concat([1]));
  var min = 0;
  var innerW = w - padding * 2;
  var innerH = h - padding * 2;
  var step = series.length > 1 ? innerW / (series.length - 1) : innerW;
  var pts = series.map(function (v, i) {
    return {
      x: padding + i * step,
      y: padding + innerH - ((v - min) / (max - min)) * innerH,
    };
  });
  ctx.clearRect(0, 0, w, h);
  if (!pts.length) return;
  ctx.beginPath();
  ctx.moveTo(pts[0].x, pts[0].y);
  for (var i = 1; i < pts.length; i++) ctx.lineTo(pts[i].x, pts[i].y);
  ctx.strokeStyle = lineColor;
  ctx.lineWidth = 2.5;
  ctx.lineJoin = "round";
  ctx.stroke();
  if (fill && pts.length) {
    ctx.lineTo(pts[pts.length - 1].x, padding + innerH);
    ctx.lineTo(pts[0].x, padding + innerH);
    ctx.closePath();
    var g = ctx.createLinearGradient(0, padding, 0, padding + innerH);
    g.addColorStop(0, "rgba(59,130,246,0.25)");
    g.addColorStop(1, "rgba(59,130,246,0)");
    ctx.fillStyle = g;
    ctx.fill();
  }
}

document.addEventListener("DOMContentLoaded", function () {
  var el = document.getElementById("chart-home-line");
  if (!(el instanceof HTMLCanvasElement)) return;
  var data = window.__IP_CHART_SERIES__;
  if (!Array.isArray(data) || !data.length) data = [0, 0, 0, 0, 0, 0, 0];
  var w = el.parentElement ? el.parentElement.clientWidth : 800;
  el.width = w;
  el.height = 220;
  ipDrawLineChart(el, data, { padding: 20 });
});
