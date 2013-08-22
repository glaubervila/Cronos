#!/bin/bash
# Contem Funcoes uteis


# conf_parser()
#    usage: conf_parser <file>
#    descr: Parses a config file, creating the
#           variables an its values
function conf_parser() {
   [ -e ${1} ] || die "Arquivo nao encontrado: $1";
   [ -r ${1} ] || die "Nao foi possivel ler o arquivo $1";
   for line in `cat ${1} | cut -d= -f1  | grep -v '#'`; do
      export ${line}=`cat ${1} | grep -v '#' | grep "${line}" | cut -d= -f2`;
   done
   return 0;
}

