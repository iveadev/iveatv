export const dateFormat = (d) => {
const formatoFecha = new Intl.DateTimeFormat('es-MX', {
    dateStyle: "medium",
    timeStyle: "short",
    timeZone: "America/Mexico_city",
  });
const _d = new Date(d);
const str = formatoFecha.format(_d);
return str;
}