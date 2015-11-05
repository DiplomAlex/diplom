<?php

class Model_Service_Menu implements Model_Service_Interface
{

    const MODE_NO_CONDITIONS  = 0;
    const MODE_USE_CONDITIONS = 1;

    protected static $_acl = NULL;
    protected static $_translator = NULL;

    /**
     * lazy init translator
     * @return Zend_Translate_Adapter
     */
    public function getTranslator()
    {
        if (self::$_translator === NULL) {
            self::$_translator = Zend_Registry::get('Zend_Translate')->getAdapter();
        }
        return self::$_translator;
    }

    public function getAllStructure(Model_Object_Interface $user = NULL, $withSpacer = TRUE)
    {
        $data = array(

            'invoice_admin' => array(
                'code' => 'invoice_admin',
                'label' => $this->getTranslator()->_('Накладные'),
                'icon' => 'icons_documents',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-document',
                'action' => 'index',
                'params' => array('filter_doctype_id' => 'invoice'),
            ),

            'shipment_admin' => array(
                'code' => 'shipment_admin',
                'label' => $this->getTranslator()->_('Заявки на отгрузку'),
                'icon' => 'ico_shipments',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-document',
                'action' => 'index',
                'params' => array('shipment-only' => TRUE),
                'pages' => array(
                    array(
                        'code' => 'shipment_admin_search',
                        'label' => $this->getTranslator()->_('Поиск по отгрузкам'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'admin-document',
                        'action' => 'index',
                        'params' => array(
                            'shipment-only' => TRUE,
                            'filter-on' => TRUE,
                        ),
                        'checkAcl' => TRUE,
                    ),
                    array(
                        'code' => 'job_admin',
                        'label' => $this->getTranslator()->_('Наряды на работу'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'admin-document',
                        'action' => 'job',
                        'checkAcl' => TRUE,
                    ),
                ),
            ),


            'news_admin' => array(
                'code' => 'news_admin',
                'label' => $this->getTranslator()->_('Новости и рассылки'),
                'icon' => 'ico_news',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-news',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'news_admin_topics',
                        'label' => $this->getTranslator()->_('Рубрики'),
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-news-topic',
                        'action' => 'index',
                    ),
                    'news_admin_add'=>array(
                        'code' => 'news_admin_add',
                        'label' => $this->getTranslator()->_('Добавить'),
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-news',
                        'action' => 'edit',
                    ),
                ),
            ),

            'supplimental_orders' => array(
                'code' => 'supplimental_orders',
                'label' => $this->getTranslator()->_('Заказ запчастей'),
                'icon' => 'ico_order_supplimental',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'nearest_product_shippings' => array(
                'code' => 'nearest_product_shippings',
                'label' => $this->getTranslator()->_('Ближайшие приходы продукции'),
                'icon' => 'ico_prods_coming',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
                'pages' => array(
                    array(
                        'code' => 'nearest_product_shippings_add',
                        'label' => $this->getTranslator()->_('Добавить запись'),
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'index',
                        'action' => 'developing',
                        'checkAcl' => TRUE,
                    )
                ),
            ),

            'transport_companies' => array(
                'code' => 'transport_companies',
                'label' => $this->getTranslator()->_('Транспортные компании'),
                'icon' => 'ico_transport',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'transport',
                'pages' => array(
                    'transport_price' => array(
                        'code' => 'transport_price',
                        'label' => $this->getTranslator()->_('Стоимость доставки'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'transport-price',
                        'action' => 'index',
                    ),
                ),


            ),

            'admin_user_list' => array(
                'code' => 'user_list',
                'icon' => 'icons_statistic',
                'label' => $this->getTranslator()->_('Список пользователей'),
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'index',
             ),


            'stats' => array(
                'code' => 'stats',
                'label' => $this->getTranslator()->_('Статистика'),
                'icon' => 'icons_statistic',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'stats',
                'action' => 'index',
                'hideTop' => TRUE,
                'pages' => array(
                    array(
                        'code' => 'stats_log',
                        'label' => $this->getTranslator()->_('Вход в сервис (логи)'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'stats',
                        'action' => 'user-login',
                    ),
                    array(
                        'code' => 'manager_admin_stats',
                        'label' => $this->getTranslator()->_('По менеджерам'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'stats',
                        'action' => 'manager',
                        'checkAcl' => TRUE,
                    ),
                    array(
                        'code' => 'shipment_admin_stats',
                        'label' => $this->getTranslator()->_('По отгрузкам'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'stats',
                        'action' => 'shipment',
                        'checkAcl' => TRUE,
                    ),
                    array(
                        'code' => 'weight_size_admin_stats',
                        'label' => $this->getTranslator()->_('По весу и объему'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'stats',
                        'action' => 'weight-size',
                        'checkAcl' => TRUE,
                    ),
                    array(
                        'code' => 'shipment_abuse_stats',
                        'label' => $this->getTranslator()->_('По претензиям'),
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'index',
                        'action' => 'developing',
                        'checkAcl' => TRUE,
                    ),
                    array(
                        'code' => 'sales_stats',
                        'label' => $this->getTranslator()->_('Сколько было продано ТА'),
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'index',
                        'action' => 'developing',
                        'checkAcl' => TRUE,
                    ),
                    array(
                        'code' => 'active_clients_stats',
                        'label' => $this->getTranslator()->_('Активные клиенты'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'stats',
                        'action' => 'active-clients',
                    ),
                    array(
                        'code' => 'transport_companies_stats',
                        'label' => $this->getTranslator()->_('По тр.компаниям'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'stats',
                        'action' => 'transport',
                        'checkAcl' => TRUE,
                    ),
                    /*array(
                        'code' => 'stats_users',
                        'label' => $this->getTranslator()->_('Список пользователей'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'admin-user',
                        'action' => 'index',
                    ),*/
                ),
            ),

            'advertisment' => array(
                'code' => 'advertisment',
                'label' => $this->getTranslator()->_('Рекламный модуль'),
                'icon' => 'reklama',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-banner',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'advertisment_add',
                        'label' => $this->getTranslator()->_('Добавить баннер'),
                        'icon' => 'ico_buy_sell',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-banner',
                        'action' => 'edit',
                    ),
                ),
            ),

            'instruction' => array(
                'code' => 'instruction',
                'label' => $this->getTranslator()->_('Инструкции для сотрудников'),
                'icon' => 'icons_stuff_instructions',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'errors' => array(
                'code' => 'errors',
                'label' => $this->getTranslator()->_('Ошибки по фирме'),
                'icon' => 'ico_joints',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'bug',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'error_admin_topics',
                        'label' => $this->getTranslator()->_('Темы ошибок'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'admin-bug',
                        'action' => 'topic',
                        'checkAcl' => TRUE,
                    ),
                ),
            ),

            'contacts' => array(
                'code' => 'contacts',
                'label' => $this->getTranslator()->_('Контакты сотрудников'),
                'icon' => 'ico_staff_contacts',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'coworkers',
            ),

            'tips' => array(
                'code' => 'tips',
                'label' => $this->getTranslator()->_('Помощь'),
                'icon' => 'ico_search',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-tip',
                'action' => 'index',
                'pages' => array(
                    'tips_add' => array(
                        'code' => 'tips_add',
                        'label' => $this->getTranslator()->_('Добавить'),
                        'icon' => 'ico_search',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-tip',
                        'action' => 'edit',
                        /*'params' => array('id'=>NULL),*/
                    ),
                    'tips_archive' => array(
                        'code' => 'tips_archive',
                        'label' => $this->getTranslator()->_('Архив'),
                        'icon' => 'ico_search',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-tip',
                        'action' => 'archive',
                    ),
                ),
            ),

            'page_admin' => array(
                'code' => 'page_admin',
                'label' => $this->getTranslator()->_('Страницы'),
                'icon' => 'icons_documents',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-page',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'page_admin_add',
                        'label' => $this->getTranslator()->_('Добавить страницу'),
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-page',
                        'action' => 'edit',
                    ),
                ),
            ),

            'change_profile' => array(
                'code' => 'change_profile',
                'label' => $this->getTranslator()->_('Изменить профиль'),
                'icon' => 'ico_my_manager',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'edit',
                'params' => array(
                    'id' => @$user->id,
                ),
            ),

            'current_shipment_client' => array(
                'code' => 'current_shipment_client',
                'label' => $this->getTranslator()->_('Ваши текущие отгрузки'),
                'icon' => 'ico_shipments',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'document',
                'action' => 'shipment',
                'params' => array('shipment-only' => TRUE),
            ),

            'shipment_history_client' => array(
                'code' => 'shipment_history_client',
                'label' => $this->getTranslator()->_('История отгрузок'),
                'icon' => 'ico_history_shipment',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'document',
                'action' => 'history',
                'params' => array('shipment-only' => TRUE),
            ),

            'shipment_search_client' => array(
                'code' => 'shipment_search_client',
                'label' => $this->getTranslator()->_('Поиск по отгрузкам'),
                'icon' => 'ico_search',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'document',
                'action' => 'shipment',
                'params' => array(
                    'filter-on' => TRUE,
                    'shipment-only' => TRUE,
                ),
            ),

            'current_shipment' => array(
                'code' => 'current_shipment',
                'label' => $this->getTranslator()->_('Ваши текущие отгрузки'),
                'icon' => 'ico_shipments',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-document',
                'action' => 'index',
                'params' => array('shipment-only' => TRUE),
            ),

            'shipment_history' => array(
                'code' => 'shipment_history',
                'label' => $this->getTranslator()->_('История отгрузок'),
                'icon' => 'ico_history_shipment',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-document',
                'action' => 'history',
                'params' => array('shipment-only' => TRUE),
            ),

            'shipment_search' => array(
                'code' => 'shipment_search',
                'label' => $this->getTranslator()->_('Поиск по отгрузкам'),
                'icon' => 'ico_search',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-document',
                'action' => 'index',
                'params' => array(
                    'filter-on' => TRUE,
                    'shipment-only' => TRUE,
                ),
            ),


            'shipment_abuse' => array(
                'code' => 'shipment_abuse',
                'label' => $this->getTranslator()->_('Претензия по отгрузкам'),
                'icon' => 'ico_shipment_abuse',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'abuse',
                'action' => 'index',
            ),

            'admin_shipment_abuse' => array(
                'code' => 'admin_shipment_abuse',
                'label' => $this->getTranslator()->_('Претензия по отгрузкам'),
                'icon' => 'ico_shipment_abuse',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'abuse',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'admin_abuse_topic',
                        'label' => $this->getTranslator()->_('Темы'),
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'admin-abuse',
                        'action' => 'topic',
                    ),
                ),
            ),


            'select_transport' => array(
                'code' => 'select_transport',
                'label' => $this->getTranslator()->_('Выбор транспортной компании'),
                'icon' => 'ico_transport',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'my-transport',
                'pages' => array(
                    'transport_price' => array(
                        'code' => 'transport_price',
                        'label' => $this->getTranslator()->_('Стоимость доставки'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'tickets',
                        'controller' => 'transport-price',
                        'action' => 'index',
                    ),
                ),
            ),

            'news' => array(
                'code' => 'news',
                'label' => $this->getTranslator()->_('Новости и рассылки'),
                'icon' => 'ico_news',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'news',
                'action' => 'index',
            ),

            'current_products' => array(
                'code' => 'current_products',
                'label' => $this->getTranslator()->_('Текущие остатки товара'),
                'icon' => 'ico_product_leavings',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'eshop' => array(
                'code' => 'eshop',
                'label' => $this->getTranslator()->_('Интернет магазин'),
                'icon' => 'ico_product_leavings',
                'route' => 'shop-index',
            ),

            'usermenu' => array(
                'code' => 'usermenu',
                'label' => $this->getTranslator()->_('Меню пользователя'),
                'icon' => 'ico_clients_active',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-user',
                'action' => 'edit',
                'params' => array(
                    'id' => @$user->id,
                ),
            ),

            'settings' => array(
                'code' => 'settings',
                'label' => $this->getTranslator()->_('Настройки'),
                'icon' => 'ico_settings',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'admin_adv_board' => array(
                'code' => 'admin_adv_board',
                'label' => $this->getTranslator()->_('Доска объявлений'),
                'icon' => 'obyavy',
                'route' => 'default',
                'module' => 'social',
                'controller' => 'advert',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'admin_adv_board_add',
                        'label' => $this->getTranslator()->_('Добавить объявление'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'social',
                        'controller' => 'advert',
                        'action' => 'edit',
                    ),
                    array(
                        'code' => 'admin_adv_board_list',
                        'label' => $this->getTranslator()->_('Список автоматов'),
                        'route' => 'default',
                        'module' => 'social',
                        'controller' => 'admin-advert',
                        'action' => 'automates',
                    ),
                ),
            ),

            'adv_board' => array(
                'code' => 'adv_board',
                'label' => $this->getTranslator()->_('Доска объявлений'),
                'icon' => 'obyavy',
                'route' => 'default',
                'module' => 'social',
                'controller' => 'advert',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'adv_board_add',
                        'label' => $this->getTranslator()->_('Добавить объявление'),
                        'route' => 'default',
                        'module' => 'social',
                        'controller' => 'advert',
                        'action' => 'edit',
                    ),
                ),
            ),


            'akt_sverki' => array(
                'code' => 'akt_sverki',
                'label' => $this->getTranslator()->_('Акт сверки'),
                'icon' => 'ico_act_reconcil',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'debet_kredit' => array(
                'code' => 'debet_kredit',
                'label' => $this->getTranslator()->_('Дебет кредит'),
                'icon' => 'icons_documents',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'discounts' => array(
                'code' => 'discounts',
                'label' => $this->getTranslator()->_('Скидки'),
                'icon' => 'ico_discount',
                'route' => 'page',
                'params' => array(
                    'seo_id' => 'discounts',
                ),
            ),

            'recieve_discount' => array(
                'code' => 'recieve_discount',
                'label' => $this->getTranslator()->_('Получить еще скидку'),
                'icon' => 'ico_get_discount',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'discount',
            ),

            'my_manager' => array(
                'code' => 'my_manager',
                'label' => $this->getTranslator()->_('Мой менеджер'),
                'icon' => 'ico_my_manager',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'my-manager',
            ),

            'your_clients' => array(
                'code' => 'your_clients',
                'label' => $this->getTranslator()->_('Ваши клиенты'),
                'icon' => 'ico_your_clients',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'user',
                'action' => 'my-clients',
            ),

            'post_supplimental_order' => array(
                'code' => 'post_supplimental_order',
                'label' => $this->getTranslator()->_('Размещение заказов на запчасти'),
                'icon' => 'ico_order_supplimental',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

            'white_ip' => array(
                'code' => 'white_ip',
                'label' => $this->getTranslator()->_('Белые IP'),
                'icon' => 'ip',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-white-ip',
                'action' => 'index',
            ),

            'issues' => array(
                'code' => 'issues',
                'label' => $this->getTranslator()->_('Цели и задачи'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'issues',
                'controller' => 'index',
                'action' => 'index',
            ),

            'admin_issues' => array(
                'code' => 'admin_issues',
                'label' => $this->getTranslator()->_('Цели и задачи'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'issues',
                'controller' => 'index',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'admin_issue_topics',
                        'label' => $this->getTranslator()->_('Темы задач'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'issues',
                        'controller' => 'admin-issue',
                        'action' => 'topic',
                    ),
                ),
            ),

            'admin_client_resource' => array(
                'code' => 'admin_client_resource',
                'label' => $this->getTranslator()->_('Для клиентов'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-client-resource',
                'action' => 'index',
            ),

            'client_resource' => array(
                'code' => 'client_resource',
                'label' => $this->getTranslator()->_('Для клиентов'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'client-resource',
                'action' => 'index',
            ),

            'admin_book_order' => array(
                'code' => 'admin_book_order',
                'label' => $this->getTranslator()->_('Заказ книги'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'tickets',
                'controller' => 'admin-book-order',
                'action' => 'index',
            ),


            'admin_article' => array(
                'code' => 'admin_article',
                'label' => $this->getTranslator()->_('Статьи'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-article',
                'action' => 'index',
                'pages' => array(
                    array(
                        'code' => 'admin_article_new',
                        'label' => $this->getTranslator()->_('Новая'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-article',
                        'action' => 'edit',
                    ),
                    array(
                        'code' => 'admin_article_topic',
                        'label' => $this->getTranslator()->_('Темы'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'kernel',
                        'controller' => 'admin-article-topic',
                        'action' => 'index',
                    ),                    
                ),
            ),
            
            
            'admin_catalog' => array(
                'code' => 'admin_catalog',
                'label' => $this->getTranslator()->_('Каталог товаров'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'catalog',
                'controller' => 'admin-item_index',
                'action' => 'index',
                'hideTop' => TRUE,
                'pages' => array(
                    array(
                        'code' => 'admin_catalog_item',
                        'label' => $this->getTranslator()->_('Товары'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'catalog',
                        'controller' => 'admin-item_index',
                        'action' => 'index',
                    ),
                    array(
                        'code' => 'admin_catalog_category',
                        'label' => $this->getTranslator()->_('Категории'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'catalog',
                        'controller' => 'admin-category',
                        'action' => 'index',
                    ),
                    array(
                        'code' => 'admin_catalog_attribute',
                        'label' => $this->getTranslator()->_('Аттрибуты'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'catalog',
                        'controller' => 'admin-attribute',
                        'action' => 'index',
                    ),
                    array(
                        'code' => 'admin_catalog_attribute_group',
                        'label' => $this->getTranslator()->_('Наборы аттрибутов'),
                        'icon' => 'darts',
                        'route' => 'default',
                        'module' => 'catalog',
                        'controller' => 'admin-attribute-group',
                        'action' => 'index',
                    ),
                    
                ),
                
                
            ),
            
            'admin_site' => array(
                'code' => 'admin_site',
                'label' => $this->getTranslator()->_('Web-сайты'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'kernel',
                'controller' => 'admin-site',
                'action' => 'index',
            ),
            
            'admin_comment' => array(
                'code' => 'admin_comment', 
                'label' => $this->getTranslator()->_('Комментарии'),
                'icon' => 'darts', 
                'route' => 'default', 
                'module' => 'kernel',
                'controller' => 'admin-comment', 
                'action' => 'index-top-new',
            ),
            
            'admin_video' => array(
                'code' => 'admin_video', 
                'label' => $this->getTranslator()->_('Видеоролики'),
                'icon' => 'darts', 
                'route' => 'default', 
                'module' => 'infosite',
                'controller' => 'admin-video', 
                'action' => 'index',
            ),
            
            
            'admin_shop_order' => array(
                'code' => 'admin_shop_order',
                'label' => $this->getTranslator()->_('Заказы из магазина'),
                'icon' => 'darts',
                'route' => 'default',
                'module' => 'checkout',
                'controller' => 'admin-order',
                'action' => 'index',
                'hideTop' => TRUE,
                'pages' => array(
                    array(
                        'code' => 'admin_shop_orders',
                        'label' => $this->getTranslator()->_('Оформленные заказы'),
                        'route' => 'default',
                        'module' => 'checkout',
                        'controller' => 'admin-order',
                        'action' => 'index',
                    ),
                    array(
                        'code' => 'admin_shop_preorders',
                        'label' => $this->getTranslator()->_('Предварительные заказы'),
                        'route' => 'default',
                        'module' => 'checkout',
                        'controller' => 'admin-preorder',
                        'action' => 'index',
                    ),
                ),
            ),

            

            'SPACER' => array(
                'code' => 'spacer',
                'label' => '-',
                'module' => 'kernel',
                'controller' => 'index',
                'action' => 'developing',
            ),

        );

        return $data;
    }

    public function getFlatStructure(Model_Object_Interface $user = NULL, $withSpacer = TRUE)
    {
        $struct = $this->getAllStructure($user, $withSpacer);
        $list = array();
        foreach ($struct as $row) {
            $list[$row['code']] = $row;
            if (isset($row['pages'])) {
                foreach ($row['pages'] as $page) {
                    $page['label'] = $row['label'].' --> ' . $page['label'];
                    $list[$page['code']] = $page;
                }
            }
        }
        return $list;
    }


    public function getStructureByRoleViewAlias($role, $mode = self::MODE_USE_CONDITIONS, Model_Object_Interface $user = NULL)
    {
        if (empty($role)) {
            $result = array();
        }
        else {
            $data = $this->getAllStructure($user);
            $result = $this->{'_getStructure_'.$role}($data, $mode);
        }
        return $result;
    }


    protected function _getStructure_director($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['invoice_admin'],
            $level1['shipment_admin'],
            $level1['admin_shipment_abuse'],

            $level1['news_admin'],
            $level1['supplimental_orders'],
            $level1['nearest_product_shippings'],

            $level1['transport_companies'],
            $level1['stats'],
            $level1['admin_user_list'],

            $level1['advertisment'],
            $level1['instruction'],
            $level1['errors'],

            $level1['contacts'],
            $level1['tips'],
            $level1['page_admin'],

            $level1['change_profile'],
            $level1['admin_adv_board'],
            $level1['white_ip'],

            $level1['admin_issues'],
            $level1['admin_client_resource'],
            $level1['admin_book_order'],
            
            $level1['admin_catalog'],
            $level1['admin_site'],
            $level1['admin_article'],
            
            $level1['admin_comment'],
            $level1['admin_shop_order'],
            $level1['admin_video'],
            
        );
    }

    protected function _getStructure_client($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        $struct = array(
            $level1['current_shipment_client'],
            $level1['shipment_history_client'],
            $level1['shipment_search_client'],

            $level1['shipment_abuse'],
            /*$level1['current_transport'],*/
            $level1['select_transport'],

            $level1['news'],
            $level1['current_products'],
            $level1['eshop'],

            $level1['usermenu'],
            $level1['settings'],
            $level1['adv_board'],

            $level1['akt_sverki'],
            $level1['debet_kredit'],
            $level1['mail'],

            $level1['discounts'],
        );
        /*if (( ! (int) Model_Service::factory('user')->getCurrent()->discount_fromform_percent)
            OR ($mode == self::MODE_NO_CONDITIONS)) {
            $struct [] = $level1['recieve_discount'];
        }*/
        $struct [] = $level1['my_manager'];
        $struct [] = $level1['client_resource'];
        return $struct;
    }

    protected function _getStructure_dealer($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        $struct = array(
            $level1['current_shipment_client'],
            $level1['shipment_history_client'],
            $level1['shipment_search_client'],

            $level1['shipment_abuse'],
            /*$level1['current_transport'],*/
            $level1['select_transport'],

            $level1['news'],
            $level1['current_products'],
            $level1['eshop'],

            $level1['usermenu'],
            $level1['settings'],
            $level1['adv_board'],

            $level1['akt_sverki'],
            $level1['debet_kredit'],
            $level1['mail'],

            $level1['discounts'],
        );
        /*if (( ! (int) Model_Service::factory('user')->getCurrent()->discount_fromform_percent)
            OR ($mode == self::MODE_NO_CONDITIONS)) {
            $struct [] = $level1['recieve_discount'];
        }*/
        $struct [] = $level1['my_manager'];
        $struct [] = $level1['client_resource'];
        return $struct;
    }


    protected function _getStructure_manager($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['shipment_admin'],
            $level1['shipment_search'],
            $level1['shipment_abuse'],

            $level1['current_products'],
            $level1['nearest_product_shippings'],
            $level1['your_clients'],

            $level1['post_supplimental_order'],
            $level1['news'],
            $level1['errors'],

            $level1['instruction'],
            $level1['contacts'],
            $level1['usermenu'],

            $level1['issues'],
            $level1['client_resource'],
        );
    }

    protected function _getStructure_stockman($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['shipment_admin'],
            $level1['shipment_abuse'],
            $level1['shipment_search'],

            $level1['transport_companies'],
            $level1['nearest_product_shippings'],
            $level1['instruction'],

            $level1['errors'],
            $level1['contacts'],
            $level1['usermenu'],

            $level1['issues'],
            $level1['client_resource'],
        );
    }

    protected function _getStructure_editor($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['invoice_admin'],
            $level1['shipment_admin'],
            $level1['shipment_search'],

            $level1['shipment_abuse'],
            $level1['transport_companies'],
            $level1['nearest_product_shippings'],

            $level1['news_admin'],
            $level1['errors'],
            $level1['instruction'],

            $level1['tips'],
            $level1['contacts'],
            $level1['usermenu'],

            $level1['admin_issues'],
            $level1['admin_client_resource'],
            $level1['admin_book_order'],
            
            $level1['admin_catalog'],
            $level1['admin_article'],
            $level1['admin_video'],
        );
    }

    protected function _getStructure_siteEditor($level1, $mode = self::MODE_USE_CONDITIONS)
    {
        return array(
            $level1['admin_catalog'],
        );
    }

    

    /**
     * process prepared structure - remove elements not allowed by acl
     * @param array
     * @param mixed Model_Object_Interface | string - user or role to check by acl
     * @return @array
     */
    public function processStructureAcl($structure, $checking)
    {
        $pages = array();
        $acl = $this->getAcl();
        $service = Model_Service::factory('user');
        if ($checking instanceof Model_Object_Interface) {
            $aclService = $service;
        }
        else {
            $aclService = Model_Service::factory('role');
        }
        foreach ($structure as $page) {
            if (isset($page['resource'])) {
                $pageResource = $page['resource'];
            }
            else {
                $pageResource = $page['code'];
            }
            if (( ! $acl->has($pageResource)) OR ($page['checkAcl']!==TRUE) OR ($aclService->isAllowedByAcl($checking, $pageResource, NULL, $acl))) {
                if (isset($page['pages']) AND is_array($page['pages'])) {
                    foreach($page['pages'] as $key=>$sub) {
                        if (isset($sub['resource'])) {
                            $subResource = $sub['resource'];
                        }
                        else {
                            $subResource = $sub['code'];
                        }
                        if (isset($page['checkAcl']) AND ($page['checkAcl']===TRUE) AND ! $aclService->isAllowedByAcl($checking, $subResource, NULL, $acl)) {
                            unset($page['pages'][$key]);
                        }
                    }
                    $page['pages'] = array_values($page['pages']);
                }
                $pages []= $page;
            }
        }
        return $pages;
    }



    /**
     * lazy init acl
     */
    public function getAcl()
    {
        if (self::$_acl === NULL) {
            self::$_acl = $this->_initAcl();
        }
        return self::$_acl;
    }



    /**
     * prepare acl
     */
    protected function _initAcl()
    {
        $acl = new Zend_Acl();
        $roles = array();
        foreach (Zend_Registry::get('config')->aclRoles as $role => $parent) {
            if (empty($parent)) {
                $parentRole = NULL;
            }
            else {
                $parentRole = $roles[$parent];
            }
            $roles[$role] = new Zend_Acl_Role($role);

            $acl->addRole($roles[$role], $parentRole);
        }

        $acl->addResource(new Zend_Acl_Resource('shipment_admin_search'));
        $acl->allow('director', 'shipment_admin_search');

        $acl->addResource(new Zend_Acl_Resource('job_admin'));
        $acl->allow('director', 'job_admin');
        $acl->allow('editor', 'job_admin');
        $acl->allow('keeper', 'job_admin');

        $acl->addResource(new Zend_Acl_Resource('admin_abuse'));
        $acl->deny('user', 'admin_abuse');
        $acl->allow('director', 'admin_abuse');

        $acl->addResource(new Zend_Acl_Resource('shipment_admin_stats'));
        $acl->allow('director', 'shipment_admin_stats');

        $acl->addResource(new Zend_Acl_Resource('shipment_abuse_stats'));
        $acl->allow('director', 'shipment_abuse_stats');

        $acl->addResource(new Zend_Acl_Resource('nearest_product_shippings_add'));
        $acl->allow('editor', 'nearest_product_shippings_add');
        $acl->deny('director', 'nearest_product_shippings_add');

        $acl->addResource(new Zend_Acl_Resource('transport_companies_stats'));
        $acl->allow('director', 'transport_companies_stats');

        $acl->addResource(new Zend_Acl_Resource('sales_stats'));
        $acl->allow('director', 'sales_stats');

        $acl->addResource(new Zend_Acl_Resource('instruction_add'));
        $acl->allow('editor', 'instruction_add');

        $acl->addResource(new Zend_Acl_Resource('error_admin_topics'));
        $acl->allow('director', 'error_admin_topics');

        return $acl;
    }

}
