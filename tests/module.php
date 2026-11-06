<?php

declare(strict_types=1);

namespace CostCenterTests\Combodo\iTop\Service\Events {
    // Minimal argument substitute; event dispatch itself requires iTop.
    class EventData {}
}

namespace CostCenterTests {
    class MetaModel
    {
        public static ?object $type = null;
        public static function GetObject(string $class, int $id, bool $required): ?object
        {
            if ($class !== 'OrderRequestType' || $id !== 1 || $required) {
                throw new \RuntimeException('Unexpected request type lookup.');
            }
            return self::$type;
        }
    }

    $model = simplexml_load_file(__DIR__ . '/../br-costcenter/datamodel.br-costcenter.xml');
    $bridge = simplexml_load_file(__DIR__ . '/../br-costcenter-bridge-for-br-orderrequest/datamodel.br-costcenter-bridge-for-br-orderrequest.xml');
    if ($model === false || $bridge === false) {
        throw new \RuntimeException('Cannot load module data models.');
    }
    $code = $bridge->xpath('//class[@id="OrderRequest"]/methods/method[@id="OnOrderRequestComputeValuesCostCenter"]/code');
    if (count($code) !== 1) {
        throw new \RuntimeException('Expected exactly one cost center callback.');
    }
    // Execute the actual local XML method, rather than duplicating its logic.
    eval('namespace CostCenterTests; trait RequestDefaults {' . (string) $code[0] . '}');

    class Request
    {
        use RequestDefaults;
        public function __construct(public array $values, public array $changes = []) {}
        public function Get(string $key): mixed { return $this->values[$key]; }
        public function Set(string $key, mixed $value): void { $this->values[$key] = $value; }
        public function ListChanges(): array { return $this->changes; }
    }

    $tests = [];
    $tests['cost center identity, hierarchy and inactive status remain defined'] = static function () use ($model): void {
        foreach ([
            '//class[@id="CostCenter"]/properties/uniqueness_rules/rule[is_blocking="true" and attributes/attribute[@id="code"] and attributes/attribute[@id="org_id"]]',
            '//class[@id="CostCenter"]/fields/field[@id="parent_id" and @xsi:type="AttributeHierarchicalKey"]',
            '//class[@id="CostCenter"]/fields/field[@id="costcenters_list" and linked_class="CostCenter" and ext_key_to_me="parent_id"]',
            '//class[@id="CostCenter"]/fields/field[@id="status" and default_value="active"]/values/value[@id="inactive"]',
            '//class[@id="CostCenter"]/properties/obsolescence/condition[.="status = \'inactive\'"]',
            '//class[@id="FunctionalCI"]/fields/field[@id="costcenter_id" and target_class="CostCenter" and is_null_allowed="true"]/dependencies/attribute[@id="org_id"]',
        ] as $path) {
            $model->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            if (count($model->xpath($path)) !== 1) {
                throw new \RuntimeException('Missing model contract: ' . $path);
            }
        }
    };
    $tests['order request callback is connected to compute values'] = static function () use ($bridge): void {
        $listeners = $bridge->xpath('//class[@id="OrderRequest"]/event_listeners/event_listener[event="EVENT_DB_COMPUTE_VALUES" and callback="OnOrderRequestComputeValuesCostCenter"]');
        if (count($listeners) !== 1) {
            throw new \RuntimeException('Missing compute-values listener.');
        }
    };
    // Name, type ID, current cost center, type changed, default (null = missing type), expected.
    foreach ([
        ['fill empty cost center', 1, null, false, 42, 42],
        ['preserve manual cost center', 1, 7, false, 42, 7],
        ['replace cost center after type change', 1, 7, true, 42, 42],
        ['clear cost center when changed type has no default', 1, 7, true, 0, null],
        ['leave empty value without default', 1, null, false, 0, null],
        ['preserve value without request type', 0, 7, true, 42, 7],
        ['preserve value when request type is missing', 1, 7, true, null, 7],
    ] as [$name, $type, $current, $changed, $default, $expected]) {
        $tests[$name] = static function () use ($type, $current, $changed, $default, $expected): void {
            MetaModel::$type = $default === null ? null : new class($default) {
                public function __construct(private int $value) {}
                public function Get(string $key): int {
                    if ($key !== 'default_costcenter_id') {
                        throw new \RuntimeException('Unexpected default field.');
                    }
                    return $this->value;
                }
            };
            $request = new Request(['request_type_id' => $type, 'costcenter_id' => $current], $changed ? ['request_type_id' => true] : []);
            $request->OnOrderRequestComputeValuesCostCenter(new Combodo\iTop\Service\Events\EventData());
            if ($request->Get('costcenter_id') !== $expected) {
                throw new \RuntimeException('Unexpected resulting cost center.');
            }
        };
    }
    return $tests;
}
