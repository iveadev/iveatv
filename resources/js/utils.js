export const dateFormat = (d) => {
const formatoFecha = new Intl.DateTimeFormat('es-MX', {
    dateStyle: "medium",
    //timeStyle: "short",
    timeZone: "America/Mexico_city",
  });
const _d = new Date(d+'T00:00');
const str = formatoFecha.format(_d);
return str;
}