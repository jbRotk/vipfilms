<?php

//字符常量
define("SP", ' ');
define("BR", "<br>");
define("LN", "\n");
define('DELIMIT', ',');

//路径常量
define("FUNCTION_PATH", APP_PATH. DS. 'application'. DS. 'function'. DS);
define("PUBLIC_PAHT", APP_PATH. DS. 'public'. DS);
define("PUBLIC_ABS_PATH", '/public/');
define("CSS_PATH", PUBLIC_ABS_PATH. 'static/css/');
define("JS_PATH", PUBLIC_ABS_PATH. 'static/js/');
define("IMG_PATH", PUBLIC_ABS_PATH. 'static/img/');
define("COMMON_PATH", PUBLIC_PAHT. '/static/common/');
define('PLUGIN_PATH', PUBLIC_ABS_PATH. 'plugins/');

define("LOG_ROOT_PATH", APP_PATH. DS. 'log');
define("LOG_PATH", LOG_ROOT_PATH. DS. date('Ymd'). DT. 'log');

//常量值
define('ADMIN_KEY', '84d86ceb65');
define('COOKIE_KEY', '1b04c22c8bbf0ad9cda884d86ceb653b');
define('COOKIE_EXPIRE_TIME', 7200);
define('_DEBUG', true);

//网络常量
const StatusContinue = 100;
const StatusSwitchingProtocols = 101;
const StatusProcessing = 102;
const StatusOK = 200;
const StatusCreated = 201;
const StatusAccepted = 202;
const StatusNonAuthoritativeInfo = 203;
const StatusNoContent = 204;
const StatusResetContent = 205;
const StatusPartialContent = 206;
const StatusMultiStatus = 207;
const StatusAlreadyReported = 208;
const StatusIMUsed = 226;
const StatusMultipleChoices = 300;
const StatusMovedPermanently = 301;
const StatusFound = 302;
const StatusSeeOther = 303;
const StatusNotModified = 304;
const StatusUseProxy = 305;
const StatusTemporaryRedirect = 307;
const StatusPermanentRedirect = 308;
const StatusBadRequest = 400;
const StatusUnauthorized = 401;
const StatusPaymentRequired = 402;
const StatusForbidden = 403;
const StatusNotFound = 404;
const StatusMethodNotAllowed = 405;
const StatusNotAcceptable = 406;
const StatusProxyAuthRequired = 407;
const StatusRequestTimeout = 408;
const StatusConflict = 409;
const StatusGone = 410;
const StatusLengthRequired = 411;
const StatusPreconditionFailed = 412;
const StatusRequestEntityTooLarge = 413;
const StatusRequestURITooLong = 414;
const StatusUnsupportedMediaType = 415;
const StatusRequestedRangeNotSatisfiable = 416;
const StatusExpectationFailed = 417;
const StatusTeapot = 418;
const StatusUnprocessableEntity = 422;
const StatusLocked = 423;
const StatusFailedDependency = 424;
const StatusUpgradeRequired = 426;
const StatusPreconditionRequired = 428;
const StatusTooManyRequests = 429;
const StatusRequestHeaderFieldsTooLarge = 431;
const StatusUnavailableForLegalReasons = 451;
const StatusInternalServerError = 500;
const StatusNotImplemented = 501;
const StatusBadGateway = 502;
const StatusServiceUnavailable = 503;
const StatusGatewayTimeout = 504;
const StatusHTTPVersionNotSupported = 505;
const StatusVariantAlsoNegotiates = 506;
const StatusInsufficientStorage = 507;
const StatusLoopDetected = 508;
const StatusNotExtended = 510;
const StatusNetworkAuthenticationRequired = 511;
const StatusSqlError = 3306;

//apis code
const API_NO_SUCCESS = StatusOK;
const API_MSG_SUCCESS = "OK";

//Error Type
const ERROR = 'ERROR';
const NORMAL = 'NORMAL';
const WARMING = 'WARMING';
const NOTICE = 'NOTICE';
const DANGER = 'DANGER';