<?php
 namespace App\Constant;

    class RolePermissionConstant
    {
        // Menu Dashboard
        const MENU_DASHBOARD = "Dashboard";
        const PERMISSION_DASHBOARD_VIEW = "can view dashboard";
        const DASHBOARD_ICON = "ph-house";

        // Menu User
        const MENU_USER = "User";
        const USER_ICON = "ph-user";
        const PERMISSION_USER_VIEW = "can view user";
        const PERMISSION_USER_CREATE = "can create user";
        const PERMISSION_USER_EDIT = "can edit user";
        const PERMISSION_USER_DELETE = "can delete user";
        const PERMISSION_USER_CHANGE_STATUS = "can change status user";
        const PERMISSION_USER_RESTORE = "can restore user";

        // Menu Artist
        const MENU_ARTIST = "Artist";
        const ARTIST_ICON = "ph-user";
        const PERMISSION_ARTIST_VIEW = "can view artist";
        const PERMISSION_ARTIST_CREATE = "can create artist";
        const PERMISSION_ARTIST_EDIT = "can edit artist";
        const PERMISSION_ARTIST_DELETE = "can delete artist";
        const PERMISSION_ARTIST_CHANGE_STATUS = "can change status artist";
        const PERMISSION_ARTIST_RESTORE = "can restore artist";
        // Menu Director
        const MENU_DIRECTOR = "Director";
        const DIRECTOR_ICON = "fa fa-user-tie";
        const PERMISSION_DIRECTOR_VIEW = "can view director";
        const PERMISSION_DIRECTOR_CREATE = "can create director";
        const PERMISSION_DIRECTOR_EDIT = "can edit director";
        const PERMISSION_DIRECTOR_DELETE = "can delete director";
        const PERMISSION_DIRECTOR_CHANGE_STATUS = "can change status director";

        //Menu Available In
        const MENU_AVAILABLE_IN = "Available In";
        const AVAILABLE_IN_ICON = "ph-folder-notch-open";
        const PERMISSION_AVAILABLE_IN_VIEW = "can view available in";
        const PERMISSION_AVAILABLE_IN_CREATE = "can create available in";
        const PERMISSION_AVAILABLE_IN_EDIT = "can edit available in";
        const PERMISSION_AVAILABLE_IN_DELETE = "can delete available in";
        const PERMISSION_AVAILABLE_IN_RESTORE = "can restore available in";
        const PERMISSION_ASSIGN_AVAILABLE_IN = "can assign available in";
        const PERMISSION_ADD_ASSIGN_AVAILABLE_IN = "can add assign available in";
        const PERMISSION_DELETE_ASSIGN_AVAILABLE_IN = "can delete assign available in";

        // Menu Cinema Branch
        const MENU_CINEMA_BRANCH = 'Cinema Branch';
        const CINEMA_BRANCH_ICON = 'ph-buildings';
        const PERMISSION_CINEMA_BRANCH_VIEW = 'can view cinema branch';
        const PERMISSION_CINEMA_BRANCH_VIEW_DETAIL = 'can view detail cinema branch';
        const PERMISSION_CINEMA_BRANCH_CREATE = 'can create cinema branch';
        const PERMISSION_CINEMA_BRANCH_CHANGE_STATUS = 'can change status cinema branch';
        const PERMISSION_CINEMA_BRANCH_EDIT ='can edit cinema branch';
        const PERMISSION_CINEMA_BRANCH_DELETE = 'can delete cinema branch';
        
        // Menu Gift
        const MENU_GIFT = 'Gift';
        const GIFT_ICON = 'ph-gift';
        const PERMISSION_GIFT_VIEW ='can view gift';
        const PERMISSION_GIFT_CREATE = 'can create gift';
        const PERMISSION_GIFT_EDIT ='can edit gift';
        const PERMISSION_GIFT_DELETE ='can delete gift';
        const PERMISSION_GIFT_CHANGE_STATUS ='can change status gift';
        const PERMISSION_GIFT_RESTORE ='can restore gift';

        // Menu Random Gift
        const MENU_RANDOM_GIFT = 'Random Gift';
        const RANDOM_GIFT_ICON = 'ph-confetti';
        const PERMISSION_RANDOM_GIFT_VIEW ='can view random gift';
        const PERMISSION_RANDOM_GIFT_DELETE ='can delete random gift';

        // Menu Artical
        const MENU_ARTICAL ='Artical';
        const ARTICAL_ICON = 'ph-article';
        const PERMISSION_ARTICAL_VIEW ='can view artical';
        const PERMISSION_ARTICAL_CREATE ='can create artical';
        const PERMISSION_ARTICAL_EDIT ='can edit artical';
        const PERMISSION_ARTICAL_DELETE ='can delete artical';
        const PERMISSION_ARTICAL_RESTORE ='can restore artical';

        // Menu Origin
        const MENU_ORIGIN = 'Origin';
        const PERMISSION_ORIGIN_VIEW = 'can view origin';
        const PERMISSION_ORIGIN_CREATE = 'can create origin';
        const PERMISSION_ORIGIN_EDIT = 'can edit origin';
        const PERMISSION_ORIGIN_DELETE = 'can delete origin';
        const PERMISSION_ORIGIN_CHANGE_STATUS = 'can change status origin';

        // Menu Film
        const MENU_FILM = 'Film';
        const FILM_ICON = 'fa fa-video';
        const PERMISSION_FILM_VIEW = 'can view film';
        const PERMISSION_FILM_CREATE = 'can create film';
        const PERMISSION_FILM_EDIT = 'can edit film';
        const PERMISSION_FILM_DELETE = 'can delete film';
        const PERMISSION_FILM_RESTORE = 'can restore film';
        const PERMISSION_FILM_SHOW_EPISODE = 'can show episode film';
        const PERMISSION_FILM_ASSIGN_AVAILABLE_IN = 'can assign available in film';
        const PERMISSION_FILM_ADD_AVAILABLE_IN = 'can add available in film';
        const PERMISSION_FILM_DELETE_AVAILABLE_IN = 'can delete available in film';
        const PERMISSION_FILM_ADD_EPISODE = 'can add episode film';
        const PERMISSION_FILM_EDIT_EPISODE = 'can edit episode film';
        const PERMISSION_FILM_DELETE_EPISODE = 'can delete episode film';
        const PERMISSION_FILM_CHANGE_STATUS_EPISODE = 'can change status episode film';
        const PERMISSION_FILM_RESTORE_EPISODE = 'can restore episode film';
        const PERMISSION_FILM_ADD_EPISODE_SUBTITLE = 'can add episode subtitle film';
        const PERMISSION_FILM_EDIT_EPISODE_SUBTITLE = 'can edit episode subtitle film';
        const PERMISSION_FILM_DELETE_EPISODE_SUBTITLE = 'can delete episode subtitle film';

        // Menu Cast
        const MENU_CAST = 'Cast';
        const PERMISSION_CAST_VIEW = 'can view cast';
        const PERMISSION_CAST_CREATE = 'can create cast';
        const PERMISSION_CAST_EDIT = 'can edit cast';
        const PERMISSION_CAST_CHANGE_STATUS = 'can change status cast';
        const PERMISSION_CAST_DELETE = 'can delete cast';
        const PERMISSION_CAST_RESTORE = 'can restore cast';

        // Menu Report Income and Expense
        const MENU_REPORT_INCOME_EXPENSE = 'report income and expense';
        const REPORT_INCOME_EXPENSE_ICON = 'ph-currency-circle-dollar';
        const PERMISSION_REPORT_INCOME_EXPENSE_VIEW = 'can view report income and expense';
        const PERMISSION_REPORT_INCOME_EXPENSE_CREATE = 'can create report income and expense';
        const PERMISSION_REPORT_INCOME_EXPENSE_EDIT = 'can edit report income and expense';
        const PERMISSION_REPORT_INCOME_EXPENSE_DELETE = 'can delete report income and expense';
        const PERMISSION_REPORT_INCOME_EXPENSE_RESTORE = 'can restore report income and expense';

        // Menu Type
        const MENU_TYPE = 'Type';
        const PERMISSION_TYPE_VIEW = 'can view type';
        const PERMISSION_TYPE_CREATE = 'can create type';
        const PERMISSION_TYPE_EDIT = 'can edit type';
        const PERMISSION_TYPE_DELETE = 'can delete type';
        const PERMISSION_TYPE_CHANGE_STATUS = 'can change status type';
        // Menu Tag
        const MENU_TAG = 'Tag';
        const PERMISSION_TAG_VIEW = 'can view tag';
        const PERMISSION_TAG_CREATE = 'can create tag';
        const PERMISSION_TAG_EDIT = 'can edit tag';
        const PERMISSION_TAG_DELETE = 'can delete tag';
        const PERMISSION_TAG_CHANGE_STATUS = 'can change status tag';
        // Menu Distributor
        const MENU_DISTRIBUTOR = 'Distributor';
        const PERMISSION_DISTRIBUTOR_VIEW = 'can view distributor';
        const PERMISSION_DISTRIBUTOR_CREATE = 'can create distributor';
        const PERMISSION_DISTRIBUTOR_EDIT = 'can edit distributor';
        const PERMISSION_DISTRIBUTOR_DELETE = 'can delete distributor';
        const PERMISSION_DISTRIBUTOR_CHANGE_STATUS = 'can change status distributor';
        // Menu Category
        const MENU_CATEGORY = 'Category';
        const PERMISSION_CATEGORY_VIEW = 'can view category';
        const PERMISSION_CATEGORY_CREATE = 'can create category';
        const PERMISSION_CATEGORY_EDIT = 'can edit category';
        const PERMISSION_CATEGORY_DELETE = 'can delete category';
        const PERMISSION_CATEGORY_CHANGE_STATUS = 'can change status category';
        //Menu Genre
        const MENU_GENRE = 'Genre';
        const PERMISSION_GENRE_VIEW = 'can view genre';
        const PERMISSION_GENRE_CREATE = 'can create genre';
        const PERMISSION_GENRE_EDIT = 'can edit genre';
        const PERMISSION_GENRE_DELETE = 'can delete genre';
        const PERMISSION_GENRE_CHANGE_STATUS = 'can change status genre';
        // Menu Version
        const MENU_VERSION = 'Version';
        const PERMISSION_VERSION_VIEW = 'can view version';
        const PERMISSION_VERSION_CREATE = 'can create version';
        const PERMISSION_VERSION_EDIT = 'can edit version';
        const PERMISSION_VERSION_DELETE = 'can delete version';
        const PERMISSION_VERSION_CHANGE_STATUS = 'can change status version';
        // Menu System Log
        const MENU_SYSTEM_LOG = 'System Log';
        const PERMISSION_SYSTEM_LOG_VIEW = 'can view system user log';
        // Menu Setting
        const MENU_SETTING = "Setting";
        const PERMISSION_SETTING_VIEW = "can view setting";
        // Menu Role
        const MENU_ROLE = "Role";
        const PERMISSION_ROLE_VIEW = "can view role";
        const PERMISSION_ROLE_CREATE = "can create role";
        const PERMISSION_ROLE_EDIT = "can edit role";
        const PERMISSION_ROLE_DELETE = "can delete role";
        const PERMISSION_ROLE_RESTORE = "can restore role";
        const PERMISSION_CHANGE_PERMISSION = "can change permission";

        // Menu System User Log
        const MENU_SYSTEM_USER_LOG = "System User Log";
        const PERMISSION_SYSTEM_USER_LOG_VIEW = "can view system user log";
    }