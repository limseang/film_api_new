<?php
 namespace App\Constant;

    class RolePermissionConstant
    {
        // Menu Dashboard
        const MENU_DASHBOARD = "Dashboard";
        const PERMISSION_DASHBOARD_VIEW = "can view dashboard";

        // Menu User
        const MENU_USER = "User";
        const PERMISSION_USER_VIEW = "can view user";
        const PERMISSION_USER_CREATE = "can create user";
        const PERMISSION_USER_EDIT = "can edit user";
        const PERMISSION_USER_DELETE = "can delete user";
        const PERMISSION_USER_RESTORE = "can restore user";

        // Menu Request
        const MENU_MISS_PLANET = "Miss Planet";
        const PERMISSION_MISS_PLANET_VIEW = "can view miss planet";
        const PERMISSION_MISS_PLANET_CREATE = "can create miss planet";
        const PERMISSION_MISS_PLANET_EDIT = "can edit miss planet";
        const PERMISSION_MISS_PLANET_DELETE = "can delete miss planet";
        const PERMISSION_MISS_PLANET_RESTORE = "can restore miss planet";
        const PERMISSION_MISS_PLANET_VOTE_HISTORY = "can view vote history";
        const PERMISSION_MISS_PLANET_COMMENT_HISTORY = "can view comment history";
        const PERMISSION_MISS_PLANET_VIEW_ANY = "can view any miss planet";

        // Menu Supplier
        const MENU_SUPPLIER = "Supplier";
        const PERMISSION_SUPPLIER_VIEW = "can view supplier";
        const PERMISSION_SUPPLIER_EDIT = "can edit supplier";

        // Menu Supplier Order
        const MENU_SUPPLIER_ORDER = "Supplier Order";
        const PERMISSION_SUPPLIER_ORDER_VIEW = "can view supplier order";
        const PERMISSION_SUPPLIER_ORDER_VIEW_ANY = "can view any supplier order";

        // Menu Deposit
        const MENU_DEPOSIT = "Deposit";
        const PERMISSION_DEPOSIT_VIEW = "can view deposit";
        const PERMISSION_DEPOSIT_VIEW_ANY = "can view any deposit";
        // Menu Transaction
        const MENU_TRANSACTION = "Transaction";
        const PERMISSION_TRANSACTION_VIEW = "can view transaction";
        const PERMISSION_TRANSACTION_VIEW_ANY = "can view any transaction";
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
        // Menu Admin
        const MENU_ADMIN = "Admin";
        const PERMISSION_ADMIN_VIEW = "can view admin";
        const PERMISSION_ADMIN_CREATE = "can create admin";
        const PERMISSION_ADMIN_EDIT = "can edit admin";
        const PERMISSION_ADMIN_DELETE = "can delete admin";
        const PERMISSION_ADMIN_RESTORE = "can restore admin";
        // Menu Set Round
        const MENU_SET_ROUND = "Set Round";
        const PERMISSION_SET_ROUND_VIEW = "can view set round";
        const PERMISSION_SET_ROUND_CREATE = "can create set round";
        const PERMISSION_SET_ROUND_EDIT = "can edit set round";
        const PERMISSION_SET_ROUND_DELETE = "can delete set round";
        const PERMISSION_SET_ROUND_RESTORE = "can restore set round";
        // Menu Vote Price
        const MENU_VOTE_PRICE = "Vote Price";
        const PERMISSION_VOTE_PRICE_VIEW = "can view vote price";
        const PERMISSION_VOTE_PRICE_EDIT = "can edit vote price";
        const PERMISSION_VOTE_PRICE_HISTORY = "can view vote price history";
        // Menu Package
        const MENU_PACKAGE = "Package";
        const PERMISSION_PACKAGE_VIEW = "can view package";
        const PERMISSION_PACKAGE_CREATE = "can create package";
        const PERMISSION_PACKAGE_EDIT = "can edit package";
        const PERMISSION_PACKAGE_DELETE = "can delete package";
        const PERMISSION_PACKAGE_RESTORE = "can restore package";
        // Menu Currency
        const MENU_CURRENCY = "Currency";
        const PERMISSION_CURRENCY_VIEW = "can view currency";
        // Menu System User Log
        const MENU_SYSTEM_USER_LOG = "System User Log";
        const PERMISSION_SYSTEM_USER_LOG_VIEW = "can view system user log";
    }
?>