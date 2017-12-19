<?php

/**
 * Created by PhpStorm.
 * @author: adelio <adelionep at gmail dot com>
 * Date: 25/07/2017
 * Time: 18:34
 */
use NFePHP\MDFe\Make300;
use NFePHP\MDFe\Tools;

class ProcessoCompletoTest extends PHPUnit_Framework_TestCase
{
    private $tools;
    private $make;
    private $objConfig;

    private function gravarJson(){
        $arquivo = fopen(PATH_CONFIG, 'w');
        fwrite($arquivo, json_encode($this->objConfig));
        fclose($arquivo);
    }

    private function configurarJson() {
        $configJson = file_get_contents(PATH_CONFIG);
        $this->objConfig = json_decode($configJson);
        $this->objConfig->razaosocial = 'EMPRESA EMITENTE';
        $this->objConfig->pathCertsFiles = PATH_BASE . 'certs' . DIRECTORY_SEPARATOR;
        $this->objConfig->certPfxName = 'certificado.pfx';
        $this->objConfig->certPassword = 'senhacertificado';
        $this->objConfig->cnpj = '00000000000000000';
        $this->objConfig->ie = '00000000000000';
        $this->objConfig->schemesMDFe = 'PL_MDFe_300';
        $this->objConfig->pathXmlUrlFileMDFe = 'mdfe_ws3.xml';
        $this->objConfig->pathMDFeFiles = PATH_BASE_XML;
        $this->gravarJson();
    }

    private function validarConfiguracoes() {
        //var_dump($this->objConfig);
        print "\n=============================== Inicio - Debug Configuracoes ===============================";
        print "\n Ambiente: {$this->objConfig->tpAmb}";
        print "\n Razao Social: {$this->objConfig->razaosocial}";
        print "\n IE: {$this->objConfig->ie}";
        print "\n CNPJ: {$this->objConfig->cnpj}";
        print "\n schemesMDFe: {$this->objConfig->schemesMDFe}";
        print "\n certPfxName: {$this->objConfig->certPfxName}";
        print "\n certPassword: {$this->objConfig->certPassword}";
        print "\n=============================== Fim - Debug Configuracoes ===============================";
    }

    private function montarMdfe() {
        $this->make = new Make300();
        $cuf = '35';
        $ano = '17';
        $mes = '07';
        $cnpj = $this->objConfig->cnpj;
        $mod = '58';
        $serie = '1';
        $numero = '2';
        $nMDF = $numero;
        $cMDF = str_pad($numero, 8, '0', STR_PAD_LEFT);
        $tpEmissao = '1';//1-Normal/2-Contingencia
        $tpAmb = '2';//1-Producao/2-homologacao
        $tpEmit = '1';//1 - Prestador de serviço de transporte/2 - Transportador de Carga Próprio
        $modal = '1';//1-Rodoviário/2-Aéreo/3-Aquaviário/4-Ferroviário.
        $dhEmi = date('Y-m-d') . 'T00:00:00-03:00';//versao 3.00
        //$dhEmi = date('Y-m-d') . 'T00:00:00';
        $procEmi = '0';//0-emissão de MDF-e com aplicativo do contribuinte/3-emissão MDF-e pelo contribuinte com aplicativo fornecido pelo Fisco
        $verProc = '1.0.0';
        $ufIni = 'SP';//SP
        $ufFim = 'GO';
        $cMunCarrega = '3550308';
        $xMunCarrega = 'Sao Paulo';
        $ufPercurso1 = 'MG';

        $xLgr = 'RUA DE EXEMPLO';
        $nro = '123';
        $xCpl = '';
        $xBairro = 'BAIRRO EXEMPLO';
        $cMun = '3550308';
        $xMun = 'Sao Paulo';
        $cep = '08310510';
        $siglaUF = 'SP';
        $fone = '';
        $email = 'teste@teste.com.br';
        $rntrc = '00000000';
        $ciot = '000000000000';
        $cInt = '';
        $placa = 'XXX0000';
        $tara = '2810';
        $capKG = '';
        $capM3 = '';
        $tpRod = '04';
        $tpCar = '02';
        $UF = 'SP';
        $xNome = 'MOTORISTA DA SILVA';
        $cpf = '00000000000';
        $chaveCte = '00000000000000000000000000000000000000000000';
        $nCte = 0;
        $cMunDescarrega = '5208707';
        $xMunDescarrega = 'Goiania';
        $qCTe = '1';
        $qNFe = '';
        $qMDFe = '';
        $vCarga = '358448.10';
        $cUnid = '01';
        $qCarga = '153.0000';


        $chave = $this->make->montaChave($cuf, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmissao, $numero);

        print "\n Chave {$chave}";
        $cDV = substr($chave, strlen($chave) -1, strlen($chave));
        print "\n Digito Chave {$cDV}";
        $this->make->taginfMDFe($chave, '3.00');
        $this->make->tagide($cuf, $tpAmb, $tpEmit, $mod, $serie, $nMDF, $cMDF, $cDV, $modal, $dhEmi, $tpEmissao, $procEmi, $verProc, $ufIni, $ufFim);
        $this->make->tagInfMunCarrega($cMunCarrega, $xMunCarrega);
        $this->make->tagInfPercurso($ufPercurso1);
        $this->make->tagemit($cnpj, $this->objConfig->ie, $this->objConfig->razaosocial, '');
        $this->make->tagenderEmit($xLgr, $nro, $xCpl, $xBairro, $cMun, $xMun, $cep, $siglaUF, $fone, $email);

        $this->make->tagInfModal('3.00');
        $this->make->tagInfContratante('00000000000', '00000000000000');
        $this->make->tagInfCIOT('12345678', '00000000000', '00000000000000');
        $this->make->tagInfANTT('12345678');
        $this->make->tagRodo($rntrc, $ciot);
        $this->make->tagCondutor($xNome, $cpf);

        $this->make->tagVeicTracao($cInt, $placa, $tara, $capKG, $capM3, $tpRod, $tpCar, $UF);

        $this->make->tagInfCTe($nCte, $chaveCte, '');

        $this->make->tagInfMunDescarga($nCte, $cMunDescarrega, $xMunDescarrega);
        if (count($this->make->erros) > 0) {
            var_dump($this->make->erros);
        }

        $this->make->tagInfResp('1');
        $this->make->tagInfSeg('SUA SEGURADORA', '00000000000000');
        $this->make->tagSeg('102030');

        $this->make->tagTot($qCTe, $qNFe, $qMDFe, $vCarga, $cUnid, $qCarga);


        $this->make->montaMDFe();
        $xml = $this->make->getXML();

        $fileName =  PATH_TEMP . $chave . '-mdfe.xml';
        file_put_contents($fileName, $xml);
        return $fileName;
    }

    private function validarMdfe($fileName) {
        $valido = false;
        $this->tools = new Tools(PATH_CONFIG);
        $this->tools->validarXml($fileName);
        if (count($this->tools->errors) > 0) {
            foreach($this->tools->errors as $errors){
                foreach($errors as $error) {
                    print "\n" . $error;
                }
            }
        } else {
            $valido = true;
        }
        return $valido;
    }

    private function assinarMdfe($fileName) {
        $this->tools = new Tools(PATH_CONFIG);
        $xml = file_get_contents($fileName);
        $xmlAssinado = $this->tools->assina($xml, true);
        $dom = new Dom();
        $dom->loadXMLString($xmlAssinado);
        $chave = $dom->getChave('infMDFe');
        $fileName =  PATH_ASSINADO . $chave . '-mdfe.xml';
        file_put_contents($fileName, $xmlAssinado);
        return $fileName;
    }

    private function enviarMdfe($fileName, $lote) {
        $this->tools = new Tools(PATH_CONFIG);
        $xmlEnviar = file_get_contents($fileName);
        $arrRetorno = array();
        $this->tools->sefazEnviaLote($xmlEnviar, '2', $lote, $arrRetorno);
        print_r($arrRetorno);
        return $arrRetorno['nRec'];
    }

    private function consultarRecibo($recibo){
        $this->tools = new Tools(PATH_CONFIG);
        $arrRetorno = array();
        $this->tools->sefazConsultaRecibo($recibo, '2', $arrRetorno);
        print_r($arrRetorno);
    }

    private function protocolarXml($numRecibo, $chaveMDFE) {
        $this->tools = new Tools(PATH_CONFIG);
        $xmlAssinado = PATH_BASE_XML . DIRECTORY_SEPARATOR . 'homologacao' . DIRECTORY_SEPARATOR . 'assinadas' . DIRECTORY_SEPARATOR . '201708' .
            DIRECTORY_SEPARATOR . $chaveMDFE . '-mdfe.xml';
        $xmlRecibo = PATH_BASE_XML . DIRECTORY_SEPARATOR . 'homologacao' . DIRECTORY_SEPARATOR . 'temporarias' . DIRECTORY_SEPARATOR . '201708' .
            DIRECTORY_SEPARATOR . $numRecibo . '-retConsReciMDFe.xml';
        $this->tools->addProtocolo($xmlAssinado, $xmlRecibo, true);
    }

    private function encerrarMdfe($chave, $tpAmb, $protocolo, $cUFEncerramento, $cMunEncerramento) {
        $this->tools = new Tools(PATH_CONFIG);
        $arrRetorno = array();
        $this->tools->sefazEncerra($chave, $tpAmb, '1', $protocolo, $cUFEncerramento, $cMunEncerramento, $arrRetorno);
        print_r($arrRetorno);
    }


    public function testPrcessoCompleto() {
        define('PATH_BASE', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR);
        define('JSON_FILE', 'fakeconfig.json');
        define('PATH_CONFIG', PATH_BASE . 'config/' . JSON_FILE);
        define('PATH_BASE_XML', PATH_BASE . DIRECTORY_SEPARATOR . 'xml' . DIRECTORY_SEPARATOR);
        define('PATH_TEMP', PATH_BASE_XML . 'temp' . DIRECTORY_SEPARATOR);
        define('PATH_ASSINADO', PATH_BASE_XML . 'assinado' . DIRECTORY_SEPARATOR);

        //define('NFEPHP_ROOT', dirname(__FILE__) . '../');

        $this->configurarJson();
        /*$fileName = $this->montarMdfe();
        $fileNameAssinado = $this->assinarMdfe($fileName);
        $valido = $this->validarMdfe(file_get_contents($fileNameAssinado));
        $nRecibo = '';
        if ($valido) {
            $nRecibo = $this->enviarMdfe($fileNameAssinado, '1');
            print "\n Recibo {$nRecibo}" ;
        }
        print "\n XML valido: " . $valido;
        if(!empty($nRecibo)) {
            $this->consultarRecibo($nRecibo);
        }*/

        //$this->protocolarXml('359000003269723', '35170812195067000108580010000000011000000010');
        //descomentar para encerrar
        //$this->encerrarMdfe('35170812195067000108580010000000011000000010', '2', '935170000046127', '52', '5208707');
    }
}
