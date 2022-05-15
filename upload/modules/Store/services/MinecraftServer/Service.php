<?php
class MinecraftServerService extends ServiceBase {
    public function __construct() {
        $id = 2;
        $name = 'Minecraft Server';
        $description = 'Connect your Minecraft Server with the NamelessMC-Store plugin to execute commands from product actions';
        $connection_settings = ROOT_PATH . '/modules/Store/services/MinecraftServer/settings/connection_settings.php';
        $action_settings = ROOT_PATH . '/modules/Store/services/MinecraftServer/settings/action_settings.php';

        parent::__construct($id, $name, $description, $connection_settings, $action_settings);
    }

    public function onConnectionSettingsPageLoad(TemplateBase $template, StoreFields $fields) {

    }

    public function onActionSettingsPageLoad(TemplateBase $template, StoreFields $fields) {

    }

    public function executeAction(Action $action, Order $order, Product $product, Payment $payment, array $placeholders) {
        // Execute this action on all selected connections
        $connections = ($action->data()->own_connections ? $action->getConnections() : $product->getConnections());
        foreach ($connections as $connection) {
            // Replace existing placeholder
            $placeholders['{connection}'] = $connection->name;

            // Replace the command placeholders
            $command = $action->data()->command;
            $command = str_replace(array_keys($placeholders), array_values($placeholders), $command);

            // Save queued command
            DB::getInstance()->insert('store_pending_actions', [
                'order_id' => $payment->data()->order_id,
                'action_id' => $action->data()->id,
                'product_id' => $product->data()->id,
                'player_id' => $order->data()->player_id,
                'connection_id' => $connection->id,
                'type' => $action->data()->type,
                'command' => $command,
                'require_online' => $action->data()->require_online,
                'order' => $action->data()->order,
            ]);
        }
    }
}

$service = new MinecraftServerService();