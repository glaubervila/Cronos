/**
 *
 */
var Main = Ext.extend(Ext.util.Observable,{

    id: 'id_Main'

    , constructor: function() {

        this.painelNorte = new Ext.Panel({
            cls: 'docs-header',
            height: 36,
            region:'north',
            xtype:'box',
            el:'header',
            border:false,
            margins: '0 0 5 0'
        });

        //tabPanel
        this.tabPanelCentral = new Ext.TabPanel({
            region      : 'center'
            , id        : 'tabPanelCentral'
            , activeTab : 0
            , enableTabScroll:true
            , plugins: new Ext.ux.TabCloseMenu()
            , defaults  : {closable: true}
            , items     : [
//             {
//                 title      : 'Portal'
//                 , contentEl  : 'content-portal'
//                 , closable : false
//             }
            ]
        });
//
        var date  = new Ext.Toolbar.TextItem({text: '', width: 100});
        var clock = new Ext.Toolbar.TextItem({text: '', width: 100});
        this.status_bar = new Ext.ux.StatusBar({
            id: 'main_statusbar'
            , region:'south'
            , busyText : 'Carregando...'
            , autoClear:2500
            , defaultText: ''
            , items: [
                '-', ' '
                , date, ' '
                , '-', ' '
                , clock, ' '
            ]
            , msg:function(t){
                var sb = Ext.getCmp('main_statusbar');
                switch(t){
                    case 'load':
                        sb.showBusy();
                        break;
                    case 'job':
                        sb.showBusy('Executando tarefa...');
                        break;
                    case 'ok':
                        sb.setStatus({
                            text: 'Opera&ccedil;&atilde;o concluida!'
                            , iconCls: 'x-status-valid'
                            , clear: true
                        });
                        break
                    case 'save':
                        sb.setStatus({
                            text: 'Registros salvos com sucesso!'
                            , iconCls: 'x-status-saved'
                            , clear: true
                        });
                        break
                    case 'saving':
                        sb.setStatus({
                            text: 'Salvando informa&ccedil;&otilde;es!'
                            , iconCls: 'x-status-saving'
                        });
                        break
                    case 'error':
                        sb.setStatus({
                            text: 'Opera&ccedil;&atilde;o n&atilde;o realizada!'
                            , iconCls: 'x-status-error'
                            , clear: true
                        });
                        break
                }
            }
        });

        this.menuLateral = new Ext.Panel({
            region: 'west'
            , id: 'MenuDinamico'
            , title: 'Menu'
            , layout: 'accordion'
            , width: 180
            , split: true
            , collapsible: false
            , layoutConfig: {
                fill: false
                , animate:true
            }
            , defaults: {
                collapsible: true
                , rootVisible: false
                , border: false
            }
            , onNodeClick: function( node ) {
                var arquivo  = node.attributes.arquivo;
                var diretorio = node.attributes.diretorio
                Ext.require.moduleUrl = diretorio;
                Ext.require(arquivo,function(){
                    // Recuperando o Menu pq nao consegui com scope
                    Ext.getCmp('MenuDinamico').addTab(node);
                })
            }
            , addTab: function( node ) {
                var titulo = node.text;
                var tabPanel = Ext.getCmp('tabPanelCentral');
                var novaAba = tabPanel.items.find(function( aba ){ return aba.title === titulo; });

                if (!novaAba) {
                    novaAba = tabPanel.add({
                        title : titulo
                        , iconCls: node.attributes.iconCls
                        , xtype: node.attributes.eXtype
                    });
                }
                tabPanel.activate(novaAba);
            }
            , _loadItens: function(){
                Ext.Ajax.request({
                    url    : 'main.php'
                    , method: 'POST'
                    , params : {
                        classe : 'Permissoes'
                        , action  : 'menu_dinamico'
                    }
                    , scope  : this
                    , success: function(response) {
                        var menus, itens, panel, root, j, i;
                        // Recuperando o Menu pq nao consegui com scope
                        var menuLateral = Ext.getCmp('MenuDinamico');
                        //2. decodificar resposta json
                        menus = Ext.decode(response.responseText);

                        //3. iterar sobre cada menu
                        Ext.each(menus,function(menu) {
                            root = new Ext.tree.TreeNode();

                            //4. extrair dados e adicionar tree ao painel accordion
                            panel = menuLateral.add({
                            xtype       : 'treepanel'
                            , title      : menu.titulo
                            , iconCls    : menu.icone
                            , root       : root
                            , listeners  : {
                                scope: this
                                , click: this.onNodeClick
                            }
                        });

                        //5. iterar sobre cada item de menu
                        Ext.each(menu.itens, function(item) {
                            //6. extrair dados e adicionar novo no a tree
                            root.appendChild({
                                text      : item.titulo
                                , iconCls : item.iconCls
                                , eXtype  : item.eXtype
                                , leaf    : true
                                , diretorio : item.diretorio
                                , arquivo : item.arquivo
                            });

                            },this)
                        },this);
                        //7. refatorar layout
                        menuLateral.doLayout();
                    }
                })
            }
        });

        //criar layout
        new Ext.Viewport({
            layout: 'border'
            , items: [
                this.tabPanelCentral
                , this.menuLateral
                , this.painelNorte
                , this.status_bar
            ]
            , listeners:{
                render:{
                    fn: function(){
                        // Carregando os Itens para o menu
                        Ext.getCmp('MenuDinamico')._loadItens();

                        // tratamento Relogio STATUS BAR
                        Ext.fly(clock.getEl().parent()).addClass('x-status-text-panel').createChild({cls:'spacer'});
                        Ext.TaskMgr.start({
                            run: function(){
                                Ext.fly(date.getEl()).update(new Date().format('d/n/Y'));
                                Ext.fly(clock.getEl()).update(new Date().format('g:i:s A'));
                            },
                            interval: 1000
                        });
                    }, delay: 100
                }
            }
        });

        Main.superclass.constructor.apply(this,arguments);
    }

});

Ext.onReady(function() {
    Ext.QuickTips.init();

	//Recuperando configuracoes
	Ext.Ajax.request({
		url    : 'main.php'
		, params : {
			classe : 'Configuracoes'
			, action: 'recupera_configuracoes'
		}
		, scope  : this
		, success: function(response){
			response = Ext.decode(response.responseText);
			if( response.success ){
				// Trocando Titulo
				var titulo = response.Titulo + ' v' + response.Versao_Global;
				document.title = titulo;
				// Trocando Favicon
				var favicon = document.getElementById('favicon');
				(newIcon =favicon.cloneNode(true)).setAttribute('href',response.Favicon);
				favicon.parentNode.replaceChild(newIcon,favicon);
				// Logo do banner
				document.getElementById('Logotipo_banner').style.background='url('+response.Logotipo_banner+') left top no-repeat';
				// Titulo do banner
				document.getElementById('Titulo_Banner').innerHTML  = response.Titulo_Banner;
			}
		}
	});


    Ext.Ajax.request({
        url    : 'main.php'
        , params : {
            classe : 'Usuarios'
            , action: 'verifica_sessao'
        }
        , scope  : this
        , success: function(response){
            response = Ext.decode(response.responseText);
            if( response.success ){
               setTimeout(function(){
                Ext.get('loading').remove();
                    Ext.get('loading-mask').remove();
                    //Ext.get('loading-mask').fadeOut({remove:true});
                }, 250);
                document.getElementById('header').style.visibility = 'visible';
                document.getElementById('usuario').innerHTML  = response.nome_usuario;
                new Main();
            }
            else {
                Ext.MessageBox.show({
                    title: 'Falha no Login'
                    , msg: 'Voc&ecirc; n&atilde;o est&aacute; logado ou sua sess&atilde;o espirou, por favor fa&ccedil;a o login e tente novamente.'
                    , buttons: Ext.MessageBox.OK
                    , icon: Ext.MessageBox.WARNING
                    , fn: function redirect_login() {
                        window.location.href = 'index.html';
                    }
                });
            }
        }
    });

});
