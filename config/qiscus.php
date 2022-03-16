<?php

return [
  "app" => [
      "id" => env("QISCUS_APP_ID", "app_id"),
      "secret_key" => env("QISCUS_APP_SECRET", "secret_key"),
      "url" => env("QISCUS_APP_URL", "http://localhost"),
      "channel_id" => env("QISCUS_CHANNEL_ID", 0),
      "namespace" => env("QISCUS_WA_NAMESPACE", "namespace")
  ]
];
