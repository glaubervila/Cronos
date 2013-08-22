/**
 *
 * @Diretorio   : Main/Modulos/Acesso/
 * @Dependencias: Usuarios_Form.js
 */

var Usuarios_Grid = Ext.extend(Ext.grid.GridPanel,{

    $depends     : ['Usuarios_Form.js']
    , border     : false
    , stripeRows : true
    , loadMask   : true

    // Atributos de controle de Permissoes
    , perm_insert: 1
    , perm_delete: 1
    , perm_update: 1
    , perm_execut: 1
    , perm_impres: 1

    , setPermissaoUpd: function(perm_update) {
        this.perm_update = perm_update;
    }

    ,initComponent: function() {
        //store do grid
        this.store = new Ext.data.JsonStore({
            url            : 'main.php'
            , root           : 'rows'
            , idProperty     : 'id_usuario'
            , totalProperty  : 'totalCount'
            , autoLoad       : true
            , autoDestroy    : true
            , baseParams     : {
                classe  : 'Usuarios'
                , action : 'getUsuarios'
                , limit  : 30
            }
            , fields:[
                {name:'id_usuario', type:'int'}
                , {name:'Grupo'   , type:'string'}
                , {name:'Nome'    , type:'string'}
                , {name:'Login'   , type:'string'}
            ]
        });

        // Store de Grupos
        var storeGrupos = new Ext.data.JsonStore({
                url              : 'main.php'
                , root           : 'rows'
                , idProperty     : 'id'
                //, autoLoad       : true
                , autoDestroy    : true
                , baseParams     : {
                    classe  : 'GruposUsuarios'
                    , action : 'getGrupos'
                }
                , fields:[
                    {name:'id'       , type:'int'}
                    , {name:'Grupo'  , type:'string'}
                ]
            });
        storeGrupos.load();

        this.Renderers = {
            grupos : function(value) {
                record = storeGrupos.getById(value);
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
            }]
            ,columns:[
            new Ext.grid.RowNumberer()
            , this.sm
            , {
                dataIndex   : 'Grupo'
                , header    : 'Grupo'
                , width     : 100
                , sortable  : true
                , renderer  : this.Renderers.grupos
            },{
                dataIndex   : 'Nome'
                , header    : 'Nome'
                , width     : 100
                , sortable  : true
            },{
                dataIndex   : 'Login'
                , header    : 'Login'
                , width     : 100
                , sortable  : true
            }]
            , sm: this.sm
            , plugins:new Ext.ux.grid.Search({
                //iconCls:'icon-zoom'
                readonlyIndexes:['id_usuario']
                , disableIndexes:['id_usuario']
                //, minChars: 3
                //, autoFocus: true
                , position: 'top'
                , mode: 'remote'
                , width: 200
            })
        })

        Usuarios_Grid.superclass.initComponent.call(this);
    }

    , initEvents: function() {
        Usuarios_Grid.superclass.initEvents.call(this);
        // depois de renderizado carrega as permissoes
        this.on('afterrender', this._onAfterRender, this);
        // Ao clicar em linha da Grid abrir janela para edição
        this.on({
            scope      : this
            , rowdblclick: this._onGridRowDblClick
        });
    }

    //Overrides

    // liberar as referências feitas quando uma interface é destruída.
    , onDestroy: function() {
        Usuarios_Grid.superclass.onDestroy.apply(this,arguments);
        //destroi a janela
        Ext.destroy(this._winUsuario)
        this._winUsuario = null;
    }

    //Listeners

    // Listener disparado ao clicar no botao "Novo Usuario"
    , _onBtnNovoUsuarioClick: function() {
        //cria janela de cadastro
        this._criaWindowUsuario();

        //seta atributos
        this._winUsuario.setUsuarioID(0);
        this._winUsuario.setPermissaoIns(this.perm_insert);

        //mostra
        this._winUsuario.show();
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
                        ids.push( arrSelecionados[i].get('id_usuario') );
                    }

                    this.el.mask('Excluindo usu&aacute;rios');
                    Ext.getCmp('main_statusbar').msg('job');

                    Ext.Ajax.request({
                        url    : 'main.php'
                        , params : {
                            classe : 'Usuarios'
                            , action: 'deleteUsuarios'
                            , 'id[]': ids
                        }
                        ,scope  : this
                        ,success: function() {
                            this.el.unmask();
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
        var usuarioID = record.get('id_usuario');

        //cria janela de cadastro
        this._criaWindowUsuario();

        //seta atributos
        this._winUsuario.setUsuarioID(usuarioID);
        // Seta as Permissoes de DELETE E UPADATE
        this._winUsuario.setPermissaoDel(grid.perm_delete);
        this._winUsuario.setPermissaoUpd(grid.perm_update);

        //mostra
        this._winUsuario.show();
    }

    // Listener disparado ao salvar a janela de cadastro, ou excluir registro. Veja aqui como utilizamos o conceito de Programacao Orientada a Eventos. A janela e uma interface individual e nao guarda referencia do grid. Todas as acoes sao feitas por listeners.
    , _onCadastroUsuarioSalvarExcluir: function() {
        //recarrega grid
        this.store.reload();
    }

    //Demais métodos

    //Metodo para criar a janela de cadastro de usuario
    , _criaWindowUsuario: function() {
       /*
        * reutilizar a janela. Para isso so criamos ela se a sua referencia nao existe ainda.
        * definir o config. option closeAction:'hide' na janela, para que ela nao seja destruida.
        */
        if(!this._winUsuario) {
            this._winUsuario = new Usuarios_Form({
                    renderTo   : this.body //restringe area da janela
                /*
                    * listener sendo adicionado ao evento personalizado salvar.
                    * Toda vez que o usuario, la na window de cadastro, clicar em salvar, os listeners associados aqui
                    * no grid serao disparados.
                    */
                , listeners  :{
                    scope  : this
                    , salvar : this._onCadastroUsuarioSalvarExcluir
                    , excluir: this._onCadastroUsuarioSalvarExcluir
                }
            });
        }

        return this._winUsuario;
    }

    // Controle de Permissoes
    , _onAfterRender: function() {
        var perm_insert;
        var perm_update;
        var perm_delete;
        var perm_execut;
        var perm_impres;

        Ext.Ajax.request({
            url : 'main.php'
            , method: 'POST'
            , scope: this
            , params : {
                classe  : 'Permissoes'
                , action: 'retorna_permissoes'
                , tela  : 'manutencao_usuarios'
            }
            , success: function ( result, request ) {
                var data = Ext.decode(result.responseText);
                perm_insert = data.ins;
                perm_delete = data.del;
                perm_execut = data.exc;
                perm_update = data.upd;
                perm_impres = data.imp;

                // Habilitando/Desabilitando os Botoes
                if (perm_insert == 0) {
                    Ext.getCmp('btnNovo').disable();
                }
                if (perm_delete == 0) {
                    Ext.getCmp('btnExcluir').disable();
                    this.perm_delete = perm_delete;
                }
                if (perm_execut == 0) {

                }
                if (perm_update == 0) {
                    this.setPermissaoUpd(perm_update);
                }
                if (perm_impres == 0) {

                }
            }
        });
    }


});

Ext.reg('e-Usuarios_Grid', Usuarios_Grid);