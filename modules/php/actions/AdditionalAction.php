<?php
namespace actions;

use ReflectionUtils;

class AdditionalAction {

    public string $id;
    public string $type;
    public \stdClass $additionalArgs;
    public bool $performed;
    public bool $optional;
    public bool $canBeUndone;

    public function __construct($type, $additionalArgs, $optional = false, $canBeUndone = true)
    {
        $this->id = AdditionalAction::newId();
        $this->type = $type;
        $this->additionalArgs = $additionalArgs;
        $this->performed = false;
        $this->optional = $optional;
        $this->canBeUndone = $canBeUndone;
    }

    private static function newId() {
        return vsprintf( '%s%s_%s_%s_%s_%s%s%s', str_split(bin2hex(random_bytes(16)), 4) );
    }


    /**
     * @param $dbObjects
     * @return AdditionalAction[]
     */
    public static function fromArray($dbObjects) {
        if (is_array($dbObjects)) {
            return array_map(fn($dbCard) => AdditionalAction::from($dbCard), array_values($dbObjects));
        }
        return [];
    }
    public static function from($dbObject)
    {
        return ReflectionUtils::rebuildAllPropertyValues($dbObject, AdditionalAction::class);
    }
}