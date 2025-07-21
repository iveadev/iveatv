export const dateFormat = (d) => {
const formatoFecha = new Intl.DateTimeFormat('es-MX', {
    dateStyle: "medium",
    //timeStyle: "short",
    timeZone: "America/Mexico_city",
  });

const _d = d.length == 10 ? new Date(d+'T00:00') : new Date(d);
const str = formatoFecha.format(_d);
return str;
}