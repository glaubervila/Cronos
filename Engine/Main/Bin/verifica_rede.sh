#!/bin/bash
#Script para testar redes
#Dispara pacotes contra Ips pre determinados
#Retorna um xml com os resultados no diretorio especificado


#VARIAVEIS
# Versao do script
versao="0.1 beta 14-01-2010"
# armazenando a hora do sistema na variável 'data'
data=`date +%d-%m-%Y-%H%M%S`




# permite só uma instância de um script
LOCK_FILE=/tmp/Engine.lock
(set -C; :  > $LOCK_FILE) 2> /dev/null
if [  $? != "0" ];  then
  echo "Lock File exists - exiting "
   exit 1
fi


# lista de ips
ip_destino="www.google.com";

echo "Teste de REDE";
echo "Disparando contra [${ip_destino}]";

ping -c 1 ${ip_destino};
echo "=========//========"
# # Retorna o milesegundo
# time= ping -c 1 ${ip_destino} | awk '$5~/packets/ {print $1}'
# echo ${time}

#resultado= ping -c 1 ${ip_destino};



#ping -c 1 ${ip_destino} | awk '/^time/ {print $2}';