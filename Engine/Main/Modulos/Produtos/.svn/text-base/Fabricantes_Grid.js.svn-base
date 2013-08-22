/**
 * @package     : Produtos
 * @name        : Fabricantes_Grid
 * @version     : 1.0
 * @Diretorio   : Main/Modulos/Produtos/
 * @Dependencias: Fabricantes_Form.js
 * @date        : 23/11/2010
 */

var Fabricantes_Grid = Ext.extend(Ext.grid.GridPanel,{

    $depends     : ['Fabricantes_Form.js']
    , id         : '5002'
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
        indice = permissoes_store.findExact('identificacao', this.id);
        record = permissoes_store.getAt(indice);
        this.perm_insert = record.data.ins;
        this.perm_delete = record.data.del;
        this.perm_update = record.data.upd;
        this.perm_execut = record.data.exc;
        this.perm_impres = record.data.imp;

        Fabricantes_Grid.superclass.constructor.apply(this,arguments);
    }

    , setPermissaoUpd: function(perm_update) {
        this.perm_update = perm_update;
    }

    ,initComponent: function() {
        //store do grid
        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'pk_id_fabricantes'
            , totalProperty  : 'totalCount'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'Fabricantes'
                , action : 'getFabricantes'
                , limit  : 30
            }
            , fields:[
                {name:'pk_id_fabricantes'   , type:'int'}
                , {name:'fabricante'        , type:'string'}
            ]
        });


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
            }]
            , columns:[
            new Ext.grid.RowNumberer()
            , this.sm
            , {
                dataIndex   : 'pk_id_fabricantes'
                , header    : 'C&oacute;digo'
                , width     : 50
                , sortable  : true
            },{
                dataIndex   : 'fabricante'
                , header    : 'Nome'
                , width     : 100
                , sortable  : true
            }]
            , sm: this.sm
            , plugins:new Ext.ux.grid.Search({
                readonlyIndexes:['pk_id_fabricantes']
                , disableIndexes:['pk_id_fabricantes']
                , position: 'top'
                , mode: 'remote'
                , width: 200
            })
        })

        Fabricantes_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Fabricantes_Grid.superclass.initEvents.call(this);
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
        Fabricantes_Grid.superclass.onDestroy.apply(this,arguments);
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
                        ids.push( arrSelecionados[i].get('pk_id_fabricantes') );
                    }

                    this.el.mask('Excluindo Registros');
                    Ext.getCmp('main_statusbar').msg('job');

                    Ext.Ajax.request({
                        url    : 'main.php'
                        , params : {
                            classe : 'Fabricantes'
                            , action: 'deleteFabricantes'
                            , 'pk_id_fabricantes[]': ids
                        }
                        ,scope  : this
                        ,success: function() {
                            this.el.unmask();
                            this.store.removeAll();
                            this.store.reload();
                            Ext.getCmp('main_statusbar').msg('ok');
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
        var registroID = record.get('pk_id_fabricantes');

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
        if(!this._winForm) {
            this._winForm = new Fabricantes_Form({
                renderTo   : this.body
                , listeners  :{
                    scope  : this
                    , salvar : this._onWinFormSalvarExcluir
                    , excluir: this._onWinFormSalvarExcluir
                }
            });
        }

        return this._winForm;
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

        }
        if (this.perm_update == 0) {

        }
        if (this.perm_impres == 0) {

        }
    }


});

Ext.reg('e-Fabricantes_Grid', Fabricantes_Grid);