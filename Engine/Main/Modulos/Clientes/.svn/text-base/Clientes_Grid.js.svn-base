/**
 *
 * @date        : 16/06/2010
 * @Diretorio   : Main/Modulos/Lojas/
 * @Dependencias: Lojas_Form.js
 */

var Clientes_Grid = Ext.extend(Ext.grid.GridPanel,{

    $depends     : ['Clientes_Form.js', 'Clientes_Exportar_Excel_Panel.js']
    , id         : 'manutencao_clientes'
    , identificacao : '4001' // Identificacao para permissoes

    , border     : false
    , stripeRows : true
    , loadMask   : true

    // Atributos de controle de Permissoes
    // 0 Desabilitado / 1 Habilitado
    , perm_insert: 0
    , perm_delete: 0
    , perm_update: 0
    , perm_execut: 0
    , perm_impres: 0

    , constructor: function() {
        // Tratamento de Permissoes
        indice = permissoes_store.findExact('identificacao', this.identificacao);
        record = permissoes_store.getAt(indice);
        this.perm_insert = record.data.ins;
        this.perm_delete = record.data.del;
        this.perm_update = record.data.upd;
        this.perm_execut = record.data.exc;
        this.perm_impres = record.data.imp;

        Clientes_Grid.superclass.constructor.apply(this,arguments);
    }

    , setPermissaoUpd: function(perm_update) {
        this.perm_update = perm_update;
    }

    ,initComponent: function() {
        //store do grid
        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'pk_id_cliente'
            , totalProperty  : 'totalCount'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'Clientes'
                , action : 'getClientes'
                , limit  : 30
            }
            , fields:[
                {name:'pk_id_cliente'   , type:'int'}
                , {name:'nome'          , type:'string'}
                , {name:'cpf'           , type:'string'}
                , {name:'cnpj'          , type:'string'}
                , {name:'rg'            , type:'string'}
                , {name:'inscricao_estadual', type:'string'}
                , {name:'telefone_fixo' , type:'string'}
                , {name:'telefone_movel', type:'string'}
            ]
        });

        this.Renderers = {
            grupos_lojas : function(value) {
                record = storeGruposLojas.getById(value);
                if(record) {
                    return record.data.Grupo;
                } else {
                    return 'Nenhum Registro encontrado';
                }
            }
        };


        this.sm = new Ext.grid.CheckboxSelectionModel();

        //demais atributos do grid
        Ext.apply(this,{
            viewConfig:{
                emptyText      : 'Nenhum registro encontrado'
                , forceFit:true
                , deferEmptyText : false
            }
            , bbar: new Ext.PagingToolbar({
                store      : this.store
                , pageSize  : 30
                , displayInfo: true
                , displayMsg: 'Mostrando resultados {0} - {1} de {2}'
                , plugins: new Ext.ux.ProgressBarPager()

            })
            ,tbar: [{
                text     : 'Novo'
                , id     : 'btnNovo'
                , iconCls: 'silk-add'
                , scope  : this
                , handler: this._onBtnNovoUsuarioClick
                , tooltip: {title:'Criar Novo Registro',text:'Click para adicionar um novo registro'}
            },{
                text     : 'Excluir'
                , id     : 'btnExcluir'
                , iconCls: 'silk-delete'
                , tooltip: {title:'Excluir Selecionados',text:'Selecione uma ou mais linhas para excluir os registros. <b>Dica:</b> Para selecionar mais de uma linha segure a tecla "Crtl"'}
                , scope  : this
                , handler: this._onBtnExcluirSelecionadosClick
            },{
                text     : 'Exportar'
                , id     : 'btnExportar'
                , iconCls: 'silk-page-white-excel'
                , scope  : this
                , handler: this._onBtnExportarClick
                , tooltip: {title:'Exportar MS Excel',text:'Click para Exportar os Registros para MS Excel'}
            },{
                text   : 'Relatorios'
                , iconCls: 'silk-printer'
                , menu: [{
                    text   : 'Clientes Contato/Endereço'
                    , id   : 'btn_relatorio_enderecos'
                    , iconCls: 'silk-printer'
                    , scope  : this
                    , handler: this.onClickBtnRelatorioPDFClick
                },{
                    text   : 'Clientes/Vendedor'
                    , id   : 'btn_relatorio_clientes_vendedor'
                    , iconCls: 'silk-printer'
                    , scope  : this
                    , handler: this.onClickBtnRelatorioClientesVendedorClick
                }]
            }]
            , columns:[
            new Ext.grid.RowNumberer()
            , this.sm
            , {
                dataIndex   : 'pk_id_cliente'
                , header    : 'C&oacute;digo'
                , width     : 50
                , sortable  : true
            },{
                dataIndex   : 'nome'
                , header    : 'Nome'
                //, width     : 100
                , sortable  : true
            },{
                dataIndex   : 'cpf'
                , header    : 'CPF'
                , hidden    : true
            },{
                dataIndex   : 'cnpj'
                , header    : 'CNPJ'
                , hidden    : true
            },{
                dataIndex   : 'telefone_fixo'
                , header    : 'Telefone Fixo'
                , width     : 50
                //, hidden    : true
            },{
                dataIndex   : 'telefone_movel'
                , header    : 'Telefone Movel'
                , width     : 50
                //, hidden    : true
            }]
            , sm: this.sm
            , plugins:new Ext.ux.grid.Search({
                readonlyIndexes:['pk_id_cliente']
                //, disableIndexes:['pk_id_cliente']
                //, menuStyle: 'radio'
                , position: 'top'
                , mode: 'remote'
                , width: 200
            })
        })

        Clientes_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Clientes_Grid.superclass.initEvents.call(this);
        // depois de renderizado carrega as permissoes
        this.on('afterrender', this._onAfterRender, this);

        // Ao clicar em linha da Grid abrir janela para edicao
        this.on({
            scope      : this
            , rowdblclick: this._onGridRowDblClick
        });
    }


    //Overrides

    , onDestroy: function() {
        // liberar as referecias feitas quando uma interface e destrui­da.
        Clientes_Grid.superclass.onDestroy.apply(this,arguments);
        //destroi a janela
        Ext.destroy(this._winForm)
        this._winForm = null;
    }

    //Listeners

    // Listener disparado ao clicar no botao "Novo Usuario"
    , _onBtnNovoUsuarioClick: function() {
        //cria janela de cadastro
        this._criaWindowForm();

        //seta atributos
        this._winForm.setRegistroID(0);

        //mostra
        this._winForm.show();
    }

    // Listener disparado ao clicar no botao "Excluir Selecionados"
    , _onBtnExcluirSelecionadosClick: function() {
        //busco selecionados
        var arrSelecionados = this.getSelectionModel().getSelections();

        if( arrSelecionados.length === 0 ) {
            Ext.Msg.alert('Aten&ccedil;&atilde;o','Selecione ao menos um registro!')
            return false;
        }
        else {
            Ext.Msg.confirm('Confirma&ccedil;&atilde;o','Deseja mesmo excluir o(s) registro(s) selecionado(s)?',function(opt){

                if(opt === 'no') {
                    return;
                }
                else {
                    var ids = [];
                    for( var i = 0 ; i < arrSelecionados.length ; i++ ) {
                        ids.push( arrSelecionados[i].get('pk_id_cliente') );
                    }

                    this.el.mask('Excluindo Registros');
                    Ext.getCmp('main_statusbar').msg('job');

                    Ext.Ajax.request({
                        url    : 'main.php'
                        , params : {
                            classe : 'Clientes'
                            , action: 'deleteClientes'
                            , 'pk_id_cliente[]': ids
                        }
                        ,scope  : this
                        ,success: function(r, o) {
                            var obj = Ext.decode(r.responseText);
                            if(obj.success){
                                this.el.unmask();
                                this.store.removeAll();
                                this.store.reload();
                                Ext.getCmp('main_statusbar').msg('ok');
                            }
                            else {
                                this.el.unmask();
                                this.store.removeAll();
                                this.store.reload();
                                Ext.MessageBox.show({ title:'Falha', msg: obj.msg+"<br> C&oacute;d: " + obj.code, buttons: Ext. MessageBox.OK, icon: Ext.MessageBox.WARNING });
                                Ext.getCmp('main_statusbar').msg('error');
                            }
                        }
                    });
                }
            },this);
        }
    }

    // Listener disparado ao clicar em alguma linha do grid
    , _onGridRowDblClick: function( grid, rowIndex, e ){
        //busca registro da linha selecionada
        var record = grid.getStore().getAt(rowIndex);

        //extrai id
        var registroID = record.get('pk_id_cliente');

        //cria janela de cadastro
        this._criaWindowForm();

        //seta atributos
        this._winForm.setRegistroID(registroID);
        //mostra
        this._winForm.show();
    }

    , _onWinFormSalvarExcluir: function() {
        //recarrega grid
        this.store.removeAll();
        this.store.reload();
    }

    //Demais metodos

    //Metodo para criar a janela de cadastro de usuario
    , _criaWindowForm: function() {
        /*
        * reutilizar a janela. Para isso so criamos ela se a sua referencia nao existe ainda.
        * definir o config. option closeAction:'hide' na janela, para que ela nao seja destruida.
        */
        //this._winForm = null;
        if(!this._winForm) {
            this._winForm = new Clientes_Form({
                //renderTo   : this.body
                listeners  :{
                    scope  : this
                    , salvar : this._onWinFormSalvarExcluir
                    , excluir: this._onWinFormSalvarExcluir
                }
            });
        }

        return this._winForm;
    }

    , _onBtnExportarClick: function() {
        //cria janela de cadastro
        this._criaWinExportarClientes();

        //mostra
        this._winExportar.show();
    }

    , _criaWinExportarClientes: function() {

        if(!this._winExportar) {
            this._winExportar = new Clientes_Exportar_Excel_Panel({
                //renderTo   : this.body
                listeners  :{
                    scope  : this
                }
            });
        }

    }

    // Controle de Permissoes
    , _onAfterRender: function() {

        if (this.perm_insert == 0) {
            Ext.getCmp('btnNovo').setVisible(false);
        }
        if (this.perm_delete == 0) {
            Ext.getCmp('btnExcluir').setVisible(false);
        }
        if (this.perm_execut == 0) {
            Ext.getCmp('btnExportar').setVisible(false);
        }
        if (this.perm_update == 0) {

        }
        if (this.perm_impres == 0) {

        }
    }


    , onClickBtnRelatorioPDFClick: function(){

        Ext.Ajax.timeout = 99999;
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Clientes_PDF'
                , action: 'Clientes_Contatos_Pdf'
            }
            , scope  : this
            , success: function(response){
                obj = Ext.decode(response.responseText);
                if( obj.success ){
                    Ext.getCmp('main_statusbar').msg('ok');

                    url = "Main/PHP/Download_Arquivo.php?file="+obj.file+'&path='+obj.path+'&filename='+obj.filename+'&mime='+obj.mime;

                    window.open(url,'_blank');

                }
                else {
                    alert('failure');
                }
            }
        });

    },

    onClickBtnRelatorioClientesVendedorClick: function(){

        Ext.Ajax.timeout = 99999;
        Ext.Ajax.request({
            url    : 'main.php'
            , params : {
                classe : 'Clientes_PDF'
                , action: 'Clientes_Vendedor_Pdf'
            }
            , scope  : this
            , success: function(response){
                obj = Ext.decode(response.responseText);
                if( obj.success ){
                    Ext.getCmp('main_statusbar').msg('ok');

                    url = "Main/PHP/Download_Arquivo.php?file="+obj.file+'&path='+obj.path+'&filename='+obj.filename+'&mime='+obj.mime;

                    window.open(url,'_blank');

                }
                else {
                    alert('failure');
                }
            }
        });
    },


});

Ext.reg('e-Clientes_Grid', Clientes_Grid);