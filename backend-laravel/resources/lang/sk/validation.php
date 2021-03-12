<?php

$extendedAttributes = [];

$locales = config('translatable.available_locales');


for ($i = 0; $i < count($locales); $i++) {
    $extendedAttributes['name_translations.' . $i . '.value'] = 'Názov ' . strtoupper($locales[$i]);
}
$extendedAttributes['name_translations'] = 'Názov';
$extendedAttributes['short_name'] = 'Skratka';
$extendedAttributes['garage_model'] = 'Model garáže';

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => ':Attribute musí byť akceptovaný.',
    'active_url'      => ':Attribute má neplatnú URL adresu.',
    'after'           => ':Attribute musí byť dátum po :date.',
    'after_or_equal'  => ':Attribute musí byť dátum po alebo presne :date.',
    'alpha'           => ':Attribute môže obsahovať len písmená.',
    'alpha_dash'      => ':Attribute môže obsahovať len písmená, čísla a pomlčky.',
    'alpha_num'       => ':Attribute môže obsahovať len písmená, čísla.',
    'array'           => ':Attribute musí byť pole.',
    'before'          => ':Attribute musí byť dátum pred :date.',
    'before_or_equal' => ':Attribute musí byť dátum pred alebo presne :date.',
    'between'         => [
        'numeric' => ':Attribute musí mať rozsah :min - :max.',
        'file'    => ':Attribute musí mať rozsah :min - :max kilobajtov.',
        'string'  => ':Attribute musí mať rozsah :min - :max znakov.',
        'array'   => ':Attribute musí mať rozsah :min - :max prvkov.',
    ],
    'boolean'        => ':Attribute musí byť pravda alebo nepravda.',
    'confirmed'      => ':Attribute konfirmácia sa nezhoduje.',
    'date'           => ':Attribute má neplatný dátum.',
    'date_equals'    => ':Attribute musí byť dátum rovnajúci sa :date.',
    'date_format'    => ':Attribute sa nezhoduje s formátom :format.',
    'different'      => ':Attribute a :other musia byť odlišné.',
    'digits'         => ':Attribute musí mať :digits číslic.',
    'digits_between' => ':Attribute musí mať rozsah :min až :max číslic.',
    'dimensions'     => ':Attribute má neplatné rozmery obrázku.',
    'distinct'       => ':Attribute je duplicitný.',
    'email'          => ':Attribute má neplatný formát.',
    'ends_with'      => ':Attribute musí obsahovať jednú z týchto hodnôt: :values.',
    'exists'         => 'Zvolený :attribute je neplatný.',
    'file'           => ':Attribute musí byť súbor.',
    'filled'         => ':Attribute je požadované.',
    'gt'             => [
        'numeric' => 'Hodnota :attribute musí byť väčšia ako :value.',
        'file'    => ':Attribute musí mať viac kilobajtov ako :value.',
        'string'  => ':Attribute musí mať viac znakov ako :value.',
        'array'   => ':Attribute musí mať viac prvkov ako :value.',
    ],
    'gte' => [
        'numeric' => 'Hodnota :attribute musí byť väčšia alebo rovná ako :value.',
        'file'    => ':Attribute musí mať rovnaký alebo väčší počet kilobajtov ako :value.',
        'string'  => ':Attribute musí mať rovnaký alebo väčší počet znakov ako :value.',
        'array'   => ':Attribute musí mať rovnaký alebo väčší počet prvkov ako :value.',
    ],
    'image'    => ':Attribute musí byť obrázok.',
    'in'       => 'Zvolený :attribute je neplatný.',
    'in_array' => ':Attribute sa nenachádza v :other.',
    'integer'  => ':Attribute musí byť celé číslo.',
    'ip'       => ':Attribute musí byť platná IP adresa.',
    'ipv4'     => ':Attribute musí byť platná IPv4 adresa.',
    'ipv6'     => ':Attribute musí byť platná IPv6 adresa.',
    'json'     => ':Attribute musí byť platný JSON reťazec.',
    'lt'       => [
        'numeric' => 'Hodnota :attribute musí byť menšia ako :value.',
        'file'    => ':Attribute musí mať menej kilobajtov ako :value.',
        'string'  => ':Attribute musí mať menej znakov ako :value.',
        'array'   => ':Attribute musí mať menej prvkov ako :value.',
    ],
    'lte' => [
        'numeric' => 'Hodnota :attribute musí byť menšia alebo rovná ako :value.',
        'file'    => ':Attribute musí mať rovnaký alebo menší počet kilobajtov ako :value.',
        'string'  => ':Attribute musí mať rovnaký alebo menší počet znakov ako :value.',
        'array'   => ':Attribute musí mať rovnaký alebo menší počet prvkov ako :value.',
    ],
    'max' => [
        'numeric' => ':Attribute nemôže byť väčší ako :max.',
        'file'    => ':Attribute nemôže byť väčší ako :max kilobajtov.',
        'string'  => ':Attribute nemôže byť väčší ako :max znakov.',
        'array'   => ':Attribute nemôže mať viac ako :max prvkov.',
    ],
    'mimes'     => ':Attribute musí byť súbor s koncovkou: :values.',
    'mimetypes' => ':Attribute musí byť súbor s koncovkou: :values.',
    'min'       => [
        'numeric' => ':Attribute musí mať aspoň :min.',
        'file'    => ':Attribute musí mať aspoň :min kilobajtov.',
        'string'  => ':Attribute musí mať aspoň :min znakov.',
        'array'   => ':Attribute musí mať aspoň :min prvkov.',
    ],
    'multiple_of'          => 'The :attribute must be a multiple of :value',
    'not_in'               => 'označený :attribute je neplatný.',
    'not_regex'            => ':Attribute má neplatný formát.',
    'numeric'              => ':Attribute musí byť číslo.',
    'password'             => 'Heslo nie je správne',
    'present'              => ':Attribute musí byť odoslaný.',
    'regex'                => ':Attribute má neplatný formát.',
    'required'             => ':Attribute je požadované.',
    'required_if'          => ':Attribute je požadované keď :other je :value.',
    'required_unless'      => ':Attribute je požadované, okrem prípadu keď :other je v :values.',
    'required_with'        => ':Attribute je požadované keď :values je prítomné.',
    'required_with_all'    => ':Attribute je požadované ak :values je nastavené.',
    'required_without'     => ':Attribute je požadované keď :values nie je prítomné.',
    'required_without_all' => ':Attribute je požadované ak žiadne z :values nie je nastavené.',
    'same'                 => ':Attribute a :other sa musia zhodovať.',
    'size'                 => [
        'numeric' => ':Attribute musí byť :size.',
        'file'    => ':Attribute musí mať :size kilobajtov.',
        'string'  => ':Attribute musí mať :size znakov.',
        'array'   => ':Attribute musí obsahovať :size prvkov.',
    ],
    'starts_with' => ':Attribute musí začínať niektorou z hodnôt: :values',
    'string'      => ':Attribute musí byť reťazec znakov.',
    'timezone'    => ':Attribute musí byť platné časové pásmo.',
    'unique'      => ':Attribute už existuje.',
    'uploaded'    => 'Nepodarilo sa nahrať :attribute.',
    'url'         => ':Attribute musí mať formát URL.',
    'uuid'        => ':Attribute musí byť platné UUID.',

    'unique_translation' => ':Attribute :LOCALE už existuje.',
    'unique_bank_loan_type' => 'Typ bankovej pôžičky s týmito parametrami už existuje.',
    'general_exception' => "Vyskytla sa chyba, skúste to znova neskôr.",
    'unauthorized' => "Na vykonanie tejto akcie nemáte oprávnenie.",
    'available_location' => "V tomto meste už vlastníte garáž.",
    'not_enough_money' => "Vaša firma namá dostatok peňazí.",
    'model_from_company' => "Na prácu s týmto objektom nemáte oprávnenie.",
    'available_garage_spot' => "V tejto garáži nie je dostatok miesta.",
    'available_driver_recruitment_agency' => "Vodiča si najala iná spoločnosť.",
    'exists_all' => "Zvolený :attribute je neplatný.",
    'available_garage_model_upgrade' => "Zvolený :attribute je neplatný.",
    'free_driver' => "Vybraný vodič nie je momentálne k dispozícii.",
    'model_in_garage' => "Vybraný :model and :compareModel nie sú z jednej garáže.",
    'model_status' => "Vybraný :attribute nie je pre túto akciu k dispozícii.",
    'driver_empty_truck_spot' => "Vybraný vodič už má priradený kamión.",
    'trailer_empty_truck_spot' => "Vybraný náves už má priradený kamión.",
    'truck_free_driver_spot' => "Zvolený kamión ma maximálne počet priradených vodičov.",
    'truck_free_trailer_spot' => "Zvolený kamión už má priradený náves.",
    'driver_assigned_truck' => "Zvolený kamión nepatrí vybranému vodičovi.",
    'trailer_assigned_truck' => "Zvolený náves nepatrí vybranému kamiónu.",
    'truck_has_driver' => "Zvolený vodič nepatrí do vybreného kamióna.",
    'truck_has_trailer' => "Zvolený náves nepatrí vybranému kamiónu.",
    'driver_in_garage' => "Zvolený vodič nie je v správnej garáži.",
    'can_update_driver_adr' => "Zvolený vodič má maximálnu triedu ADR.",
    'empty_garage' => "Zvolená garáž nie je prázdna.",
    'can_delete_trailer' => "Zvolený náves je stále priradení ku kamiónu.",
    'can_delete_driver' => "Zvolený vodič je stále priradení ku kamiónu.",
    'can_delete_truck' => "Zvolený kamión má stále priradeného vodiča alebo náves.",
    'can_delete_user' => "Nie je možné vyhodiť seba samého.",
    'available_market' => "Táto ponuka už nie je k dispozícii.",
    'available_order' => "Vybraná objednávka už nie je k dispozícii na priradenie.",
    'truck_for_order' => "Zvolení vodiči nie sú vhodní pre túto objednávku.",
    'maximum_bank_loans' => "Dosiahli ste maximálny počet aktívnych pôžičiek.",
    'exists_company_owner' => "Vaša firma potrebuje aspoň jedného vlastníka.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => $extendedAttributes

];
