<?php

namespace Main\Model;

use Rubix\Mvc\Model;

class Usuario extends Model {

    public function init() {

    }

    public function fAutenticarUsuario($user, $pass, $ip, $sessionID) {

        /*$dml = <<< DML
      DECLARE
          rAutenticacao {$this->owner}loja.recAutenticacao;
      BEGIN
          :codRetorno := {$this->owner}loja.fAutenticarUsuario(:codUsuario,
                                                            :desSenha,
                                                            :numIP,
                                                            rAutenticacao,
                                                            :idSessaoUsuario);
          :nomUsuario             := rAutenticacao.vNomeUsuario;
          :prkCliente             := rAutenticacao.nChaveCliente;
          :prkPerfil              := rAutenticacao.nPerfil;
          :indExibirAjuda         := rAutenticacao.nExibirAjuda;
          :indExibirPergunta      := rAutenticacao.nExibirPergunta;
          :indAutoRolar           := rAutenticacao.nAutoRolar;
          :indAutoAvancar         := rAutenticacao.nAutoAvancar;
          :desUrlBlog             := rAutenticacao.vUrlBlog;
          :indSessaoAberta        := rAutenticacao.nIndSessaoAberta;
          :indTipoPerfil          := rAutenticacao.nTipoPerfil;
          :nIndCampoPfPj          := rAutenticacao.nIndCampoPfPj;
          :mensagemDeErro         := rAutenticacao.vMensagemErro;
          :CPFDocumentoRg         := rAutenticacao.nCPFClienteRG;
          :CPFReceitaFederal      := rAutenticacao.nCPFReceitaFederal;
          :CPFAlertaBase          := rAutenticacao.nCPFAlerta;
          :VlrTransacao           := rAutenticacao.nValorTransacao;
          :nValorInicialTransacao := rAutenticacao.nValorInicialTransacao;
          :nValorFinalTransacao   := rAutenticacao.nValorFinalTransacao;
          :indAgenciaConta        := rAutenticacao.nIndAgenciaConta;
          :nIndLojaContrato       := rAutenticacao.nIndLojaContrato;
          :indAtualizarDados      := rAutenticacao.nIndAtualizarDadosCadastral;
          :nExibirPainelControle  := rAutenticacao.nExibirPainelControle;
          :sHoraInicial           := rAutenticacao.hHoraInicial;
          :sHoraFinal             := rAutenticacao.hHoraFinal;
          :nNivelHierarquico      := rAutenticacao.nNivelHierarquico;
          :nomNivelHierarquico    := rAutenticacao.nomNivelHierarquico;
      END;
DML;

        $input = array(
            'codUsuario' => $user,
            'desSenha' => $pass,
            'numIP' => $ip,
            'idSessaoUsuario' => $sessionID
        );

        $output = array(
            'codRetorno' => null,
            'nomUsuario' => null,
            'prkCliente' => null,
            'prkPerfil' => null,
            'indExibirAjuda' => null,
            'indExibirPergunta' => null,
            'indAutoRolar' => null,
            'indAutoAvancar' => null,
            'desUrlBlog' => null,
            'indSessaoAberta' => null,
            'indTipoPerfil' => null,
            'mensagemDeErro' => null,
            'CPFDocumentoRG' => null,
            'CPFReceitaFederal' => null,
            'CPFAlertaBase' => null,
            'VlrTransacao' => null,
            'nValorInicialTransacao' => null,
            'nValorFinalTransacao' => null,
            'indAgenciaConta' => null,
            'nIndLojaContrato' => null,
            'indAtualizarDados' => null,
            'sHoraInicial' => null,
            'sHoraFinal' => null,
            'nExibirPainelControle' => null,
            'nNivelHierarquico' => null,
            'nomNivelHierarquico' => null,
            'nIndCampoPfPj' => null
        );

        return $this->callProcedure($dml, $input, $output);*/
    }

    public function fEncerrarSessaoUsuario($sessionID) {

        /*$dml = <<< DML
        BEGIN
            :codRetorno := ({$this->owner}loja.fEncerrarSessaoUsuario(:idSessaoUsuario));
        END;
DML;

        $input = array(
            'idSessaoUsuario' => $sessionID
        );

        $output = array(
            'codRetorno' => null
        );

        return $this->callProcedure($dml, $input, $output);*/
    }

    public function fConsultarDadosConta($prkCliente, $desMatricula) {

        /*$dml = <<< DML
        SELECT
            vCampo AS des_campo,
            vValor AS des_valor
        FROM
            TABLE({$this->owner}preditivo.fConsultarDadosConta(:prkCliente, :desMatricula))
DML;

        $input = array(
            'prkCliente' => $prkCliente,
            'desMatricula' => $desMatricula
        );

        return $this->findBySql($dml, $input);*/
    }

}