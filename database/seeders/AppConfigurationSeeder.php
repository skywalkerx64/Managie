<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppConfiguration;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppConfigurationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run()
  {
    $app_config = [
      [
        'name' => 'String Conf',
        'code' => 'str-conf',
        'value' => 'str',
        'type' => AppConfiguration::STRING_TYPE,
      ],
      [
        'name' => 'numeric conf',
        'code' => 'numeric-conf',
        'value' => "542",
        'type' => AppConfiguration::NUMERIC_TYPE,
      ],
      [
        'name' => 'Bool conf',
        'code' => 'bool-conf',
        'value' => true,
        'type' => AppConfiguration::BOOLEAN_TYPE,
      ],
      [
        'name' => 'array conf',
        'code' => 'array-conf',
        'value' => "element 1,element 2",
        'type' => AppConfiguration::ARRAY_TYPE,
      ],
      [
        'name' => 'Password Reset Token Lifetime',
        'code' => 'auth.password.reset.token.lifetime',
        'value' => 10,
        'type' => AppConfiguration::NUMERIC_TYPE,
      ],

      //Grant permissions per user
      [
        'name' => 'Grant permissions per user',
        'code' => Permission::GRANT_PER_USER_CONF_CODE,
        'value' => false,
        'type' => AppConfiguration::BOOLEAN_TYPE,
      ],
      [
        'name' => 'Ajout de la clé fedapay',
        'code' => 'config.fedapay.key',
        'value' => 'fedapay',
        'type' => AppConfiguration::STRING_TYPE,
      ],
      [
        'name' => 'Ajout de la clé kkiapay',
        'code' => 'config.kkiapay.key',
        'value' => 'kkiapay',
        'type' => AppConfiguration::STRING_TYPE,
      ],
      [
        'name' => 'Autoriser la modification de mot de passe par le collaborateur.',
        'code' => 'login.configuration.forgotPassword',
        'value' => true,
        'type' => AppConfiguration::BOOLEAN_TYPE,
      ],

      [
        'name' => 'Permettre la connexion avec un OTP.',
        'code' => 'login.configuration.enable_otp',
        'value' => false,
        'type' => AppConfiguration::BOOLEAN_TYPE,
      ]

    ];

    AppConfiguration::insert($app_config);
  }
}
