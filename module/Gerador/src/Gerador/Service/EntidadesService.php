<?php

namespace Gerador\Service;

use FS\Model\Service;

class EntidadesService extends Service {

    public function init() {
        
    }

    public function getColunas($schema, $table) {
        $sql = <<<SQL
        select
				tabela.*,
				case when tabela.tipo_de_dado <> 'numerico' then
					tamanho
				else
					tamanho/65536
				end as tamanho,
				pk.campo_pk,
				fk.constraint,
				fk.esquema_fk,
				fk.tabela_fk,
				fk.campo_fk,
				uk.unique_key
			from 
				(SELECT 
					n.nspname as esquema, 
					c.relname as tabela, 
					a.attname as campo,
					
					case 
						when a.attnotnull = 't' then 'not null'
						when a.attnotnull = 'f' then ''
					end as obrigatorio,
					format_type(t.oid, null) as tipo,
					case 
						when format_type(t.oid, null) = 'bigint' then 'numerico'
						when format_type(t.oid, null) = 'bigserial' then 'numerico'
						when format_type(t.oid, null) = 'bit' then 'numerico'
						when format_type(t.oid, null) = 'bit varying' then 'numerico'
						when format_type(t.oid, null) = 'boolean' then 'numerico'
						when format_type(t.oid, null) = 'character varying' then 'texto'
						when format_type(t.oid, null) = 'character' then 'texto'
						when format_type(t.oid, null) = 'date' then 'data'
						when format_type(t.oid, null) = 'double precision' then 'numerico'
						when format_type(t.oid, null) = 'integer' then 'numerico'
						when format_type(t.oid, null) = 'interval' then 'numerico'
						when format_type(t.oid, null) = 'money' then 'numerico'
						when format_type(t.oid, null) = 'numeric' then 'numerico'
						when format_type(t.oid, null) = 'real' then 'numerico'
						when format_type(t.oid, null) = 'smallint' then 'numerico'
						when format_type(t.oid, null) = 'serial' then 'numerico'
						when format_type(t.oid, null) = 'text' then 'texto'
						when format_type(t.oid, null) = 'time with time zone' then 'data'
						when format_type(t.oid, null) = 'timestamp' then 'data'
						when format_type(t.oid, null) = 'timestamp with time zone' then 'data'
						when format_type(t.oid, null) = 'timestamp without time zone' then 'data'
						else coalesce(format_type(t.oid, null), 'tdata')
					end as tipo_de_dado					
					, case 
						when a.attlen >= 0 then a.attlen
						else (a.atttypmod-4)
					  END AS tamanho
					, cm.description as descricao
				FROM	pg_attribute a
					inner join pg_class c
						on a.attrelid = c.oid
					inner join pg_namespace n
						on c.relnamespace = n.oid
					inner join pg_type t
						on a.atttypid = t.oid
					left join pg_description cm
						on a.attrelid::oid = cm.objoid::oid
						and attnum = cm.objsubid
				WHERE 
					c.relkind = 'r'                       -- no indices
					and n.nspname not like 'pg\\_%'       -- no catalogs
					and n.nspname != 'information_schema' -- no information_schema
					and a.attnum > 0                      -- no system att's
					and not a.attisdropped                -- no dropped columns
				) as tabela 
				left join 
				(SELECT
					pg_namespace.nspname AS esquema,
					pg_class.relname AS tabela,
					pg_attribute.attname  AS campo_pk
				FROM 
					pg_class
					JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace AND
					pg_namespace.nspname NOT LIKE E'pg_%'
					JOIN pg_attribute ON pg_attribute.attrelid=pg_class.oid AND
					pg_attribute.attisdropped='f'
					JOIN pg_index ON pg_index.indrelid=pg_class.oid AND
					pg_index.indisprimary='t' AND (
						pg_index.indkey[0]=pg_attribute.attnum OR
						pg_index.indkey[1]=pg_attribute.attnum OR
						pg_index.indkey[2]=pg_attribute.attnum OR
						pg_index.indkey[3]=pg_attribute.attnum OR
						pg_index.indkey[4]=pg_attribute.attnum OR
						pg_index.indkey[5]=pg_attribute.attnum OR
						pg_index.indkey[6]=pg_attribute.attnum OR
						pg_index.indkey[7]=pg_attribute.attnum OR
						pg_index.indkey[8]=pg_attribute.attnum OR
						pg_index.indkey[9]=pg_attribute.attnum
					)
				) as pk
				on (
					tabela.esquema = pk.esquema
					and tabela.tabela = pk.tabela
					and tabela.campo = pk.campo_pk
				)
				left join 
				(SELECT
					n.nspname AS esquema,
					cl.relname AS tabela,
					a.attname AS campo,
					ct.conname AS constraint,
					nf.nspname AS esquema_fk,
					clf.relname AS tabela_fk,
					af.attname AS campo_fk
					--pg_get_constraintdef(ct.oid) AS criar_sql
				FROM 
					pg_catalog.pg_attribute a
					JOIN pg_catalog.pg_class cl ON (a.attrelid = cl.oid AND cl.relkind= 'r')
					JOIN pg_catalog.pg_namespace n ON (n.oid = cl.relnamespace)
					JOIN pg_catalog.pg_constraint ct ON (a.attrelid = ct.conrelid AND ct.confrelid != 0 AND ct.conkey[1] = a.attnum) 
					JOIN pg_catalog.pg_class clf ON (ct.confrelid = clf.oid AND clf.relkind = 'r')
					JOIN pg_catalog.pg_namespace nf ON (nf.oid = clf.relnamespace)
					JOIN pg_catalog.pg_attribute af ON (af.attrelid = ct.confrelid AND af.attnum = ct.confkey[1])
				) as fk
				on (
					tabela.esquema = fk.esquema
					and tabela.tabela = fk.tabela
					and tabela.campo = fk.campo
				)
				left join
                (

                    SELECT
                        ic.relname AS index_name,
                        bc.relname AS tab_name,
                        ta.attname AS column_name,
                        i.indisunique AS unique_key,
                        i.indisprimary AS primary_key
                    FROM
                        pg_class bc,
                        pg_class ic,
                        pg_index i,
                        pg_attribute ta,
                        pg_attribute ia
                    WHERE
                        bc.oid = i.indrelid
                        AND ic.oid = i.indexrelid
                        AND ia.attrelid = i.indexrelid
                        AND ta.attrelid = bc.oid
                        AND ta.attrelid = i.indrelid
                        AND ta.attnum = i.indkey[ia.attnum-1]
                    ORDER BY
                        index_name, tab_name, column_name
                ) as uk
                on
                (

                    tabela.tabela = uk.tab_name
                    and tabela.campo = uk.column_name
                )			where
			    tabela.esquema = '{$schema}' and tabela.tabela = '{$table}'
			order by
				tabela.esquema, 
				tabela.tabela,
				pk.campo_pk,
				fk.campo_fk
SQL;

        $stm = $this->getAdapter()->query($sql);

        $return = array();
        foreach ($stm->execute() as $_value) {
            $return[$_value['campo']] = $_value;
        }

        return $return;
    }

}
