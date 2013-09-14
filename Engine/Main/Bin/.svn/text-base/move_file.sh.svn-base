#!/bin/bash
# Programa que move um arquivo do diretorio do apache para Outro diretorio
# Recebe 2 parametros
#<caminho do Arquivo>
#<caminho do Arquivo
cp -rf $1 $2

if [ -r ${2} ]; then
    rm -rf $1
else
    echo -e "falha arquivo nao foi movido"
fi
exit