/**
 * List Calendarのトップレベルオブジェクト
 *
 * @module List Calendar
 */
function Listcalendar() {}

/**
 *  名前空間を設定・管理
 *
 *  <p>
 *  引数に対応する既存のオブジェクトが存在するときはそのオブジェクトを返す。
 *  存在しないときは空のオブジェクト作成・登録してして返す。
 *  </p>
 *
 *  @param {String} name オブジェクト名
 *  @return {Object} 引数にマップされたオブジェクト
 */

/**
 * 名前空間管理
 *
 * @method 名前空間管理
 * @type {namespace}
 */
Listcalendar.namespace = function() {
    var objectList = {};
    function namespace( name ) {
        if ( typeof objectList[ name ] === 'undefined' ) {
            objectList[ name ] = {};
        }
        return objectList[ name ];
    }
    return namespace;
}();