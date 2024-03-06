####################################################################
# Gabriel Rocha
# Procedimento: INSTALAÇÃO GLPI NO UBUNTU
# @Responsável: grgsouza1@gmail.com
# @Data: 05/02/2024
# @Versão: 1.0
# @Homologado: Ubuntu 22.04
####################################################################

# parametros ajustaveis
DEST_DIR=/backup
TIMESTAMP=$(date +%Y%m%d%H%M%S)
DB_NAME="glpi"
MANTER_VERSOES=7

# registra no log o inicio da operacao de backup
logger "iniciando backup do GLPI..."

# exporta o banco de dados
mysqldump --databases $DB_NAME > /var/www/glpi/glpi.sql

# copia as pastas de dados e o banco tambem
tar -czvf ${DEST_DIR}/GLPI_${TIMESTAMP}.tar.gz \
        /var/www/glpi \
        /var/lib/glpi \
        /etc/glpi     \
        /etc/apache2/sites-available


# removendo arquivos temporarios
rm -rf /var/www/glpi/glpi.sql


# removendo backups mais antigos que $MANTER_VERSOES
skip=0
ls -c $DEST_DIR | while read line; do
        skip=$(($skip + 1));
        if [ $skip -gt $MANTER_VERSOES ]; then
                logger "removendo backup antigo do glpi $line"
                rm -rf $DEST_DIR/$line
        fi
done

logger "finalizado operacao de backup do glpi"
