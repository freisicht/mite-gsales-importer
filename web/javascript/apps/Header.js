/**
 * Created by Dev on 28.10.16.
 */

var headerApp = new Vue({
    el: '#header',
    data: {
        // Enums
        TYPE_INFO: "info",
        TYPE_SUCCESS: "success",
        TYPE_WARNING: "warning",
        infotext: "",
        infotype: "success",
        loadingInterval: null,
        isOutputting: false,
        displayTime: 0,
        messages: []
    },
    methods: {
        addMsg: function (msg, type, displayTime = 0) {
            this.messages.push({msg: msg, type: type, displayTime: displayTime});
            this.outputMessages();
        },
        addSuccess: function (msg, displayTime = 0) {
            this.addMsg(msg, this.TYPE_SUCCESS, displayTime);
        },
        addInfo: function (msg, displayTime = 0) {
            this.addMsg(msg, this.TYPE_INFO, displayTime);
        },
        addWarning: function (msg, displayTime = 0) {
            this.addMsg(msg, this.TYPE_WARNING, displayTime);
        },
        outputMessages: function () {
            if (this.messages.length == 0 || this.isOutputting) {
                return;
            }

            this.isOutputting = true;

            let message      = this.messages.shift();
            this.infotext    = message.msg;
            this.infotype    = message.type;
            this.displayTime = message.displayTime;

            setTimeout(() => {
                this.isOutputting = false;

                if (this.messages.length > 0) {
                    this.outputMessages();
                }
            }, this.displayTime)
        },
        setIsLoading: function () {
            if (this.loadingInterval) {
                return;
            }

            let points = "";
            this.loadingInterval = setInterval(() => {
                if (points == "...")
                    points = "";

                points += ".";

                this.addInfo("Lade" + points);
            }, 500)
        },
        stopIsLoading: function () {
            if (this.loadingInterval) {
                clearInterval(this.loadingInterval);
                this.loadingInterval = null;
            }
        }
    }
});
