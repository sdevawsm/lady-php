<?php

namespace LadyPHP\Routing;

/**
 * Alias para RouteFacade
 * 
 * Mantém compatibilidade com código existente que usa Route::get(), etc.
 */
class_alias(RouteFacade::class, Route::class); 