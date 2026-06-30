if (!String.prototype.toJson) {
    String.prototype.toJson = function () {
        try {
            // this 指向当前字符串（注意：可能是 String 对象，所以转成原始字符串）
            return JSON.parse(this.toString());
        } catch (e) {
            return null; // 解析失败返回 null（你也可以返回 {} 或 undefined）
        }
    };
}