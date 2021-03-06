export function getTaxaPage(clientRoot, tid) {
  return `${clientRoot}/taxa/index.php?taxon=${tid}`;
}

export function getCommonNameStr(item) {
  const basename = item.vernacular.basename;
  const names = item.vernacular.names;

  let cname = basename;
  if (names.length > 0) {
    cname = names[0];
  }

  if (cname.includes(basename) && basename !== cname) {
    return `${basename}, ${cname.replace(basename, '')}`
  }

  return cname;
}