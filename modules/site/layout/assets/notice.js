(function() {
    'use strict';

    // Notices service
    window.Notice = new Vue({
        el: '#Notice',
        template: `
            <div class="notice" v-if="items.length">
                <div class="notice__darken" v-if="item.darken"></div>
                <div class="notice__wrap" @mouseover.once="item.lifetime > 0 ? (item.lifetime = 0) || clearTimeout(item._timeout) : null" :style="{width: item.width + 'px'}" :class="[item.type, 'notice__wrap_x-' + item.x, 'notice__wrap_y-' + item.y]" :key="item">
                    <div class="notice__close" v-if="item.canclose" @click="close(item)"></div>
                    <i class="notice__icon fa" v-if="item.icon" :class="['fa-' + item.icon]"></i>
                    <div class="notice__body">
                        <div class="notice__title" v-if="item.title" v-html="item.title"></div>
                        <div v-if="item.text" v-html="item.text"></div>
                        <div class="notice__btns">
                            <button v-for="(v, k) in item.buttons" @click="v(item) !== false ? close(item) : null" v-text="k"></button>
                        </div>
                    </div>
                    <div v-if="item.lifetime > 0" class="notice__progress" :style="{animation: 'notice__progress_anim ' + (item.lifetime / 1000) + 's linear'}"></div>
                </div>
            </div>
        `,
        data: {
            items: []
        },
        mounted() {
            let s = document.createElement('style');
            s.innerHTML = `
                .notice {}
                    .notice__darken { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); }
                    .notice__wrap { position:fixed; z-index:1002; background:#fff; color:#000; box-shadow:0 4px 12px rgba(0,0,0,.1); overflow:hidden; border-radius:3px; }
                        .notice__wrap.primary { background:var(--primary-color); color:#fff; }
                        .notice__wrap.secondary { background:var(--secondary-color); color:#fff; }
                        .notice__wrap.success { background:var(--success-color); color:#fff; }
                        .notice__wrap.error { background:var(--error-color); color:#fff; }
                        .notice__wrap.warning { background:var(--warning-color); color:#fff; }
                        .notice__wrap_x-left { left:30px; }
                        .notice__wrap_x-center { left:50%; transform:translateX(-50%); }
                        .notice__wrap_x-right { right:30px; }
                        .notice__wrap_y-top { top:30px; }
                        .notice__wrap_y-center { top:50%; transform:translateY(-50%); }
                        .notice__wrap_y-bottom { bottom:30px; }
                        .notice__wrap_x-center.notice__wrap_y-center { transform:translate(-50%); }
                    .notice__close { position:absolute; top:8px; right:8px; opacity:0.3; cursor:pointer; padding:15px; background:url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTQiIGhlaWdodD0iMTQiIHZpZXdCb3g9IjAgMCAxNCAxNCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48dGl0bGU+Y2xvc2U8L3RpdGxlPjxnIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlPSIjMDAwIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0xIDFsMTIgMTJNMTMgMUwxIDEzIi8+PC9nPjwvc3ZnPg==) center center no-repeat; }
                        .notice__close:hover { opacity:0.7; }
                    .notice__icon { font-size:21px; position:absolute; top:13px; left:13px; }
                    .notice__body { padding:15px 45px 15px 20px; }
                        .notice__icon ~ .notice__body { padding-left:50px; }
                    .notice__title { font-size:15px; font-weight:600; margin:0 0 5px; }
                    .notice__btns > * { margin:10px 0 0; border:1px solid rgba(0,0,0,0.3); cursor:pointer; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.1); color:#000; padding:7px 20px; border-radius:2px; transition:0.2s; }
                        .notice__btns > *:hover { box-shadow:0 1px 4px rgba(0,0,0,.2); }
                    .notice__progress { position:absolute; bottom:0; left:0; height:5px; width:100%; background:rgba(0,0,0,0.3); }
                        @keyframes notice__progress_anim {0% { width:100%; } 100% { width:0%; } }
            `;
            document.head.appendChild(s);
        },
        computed: {
            item() { return this.items[this.items.length - 1]; }
        },
        methods: {
            show(text, params) {
                let item = Object.assign({x: 'center', y: 'top', width: 320, type: '', icon: '', text: text, title: '', buttons: {}, lifetime: 5000, canclose: true, darken: false}, params || {});
                this.items.push(item);
                if(item.lifetime > 0) item._timeout = setTimeout(() => { this.close(item); }, item.lifetime);
            },
            close(item) {
                if(item._timeout) clearTimeout(item._timeout);
                this.items.splice(this.items.indexOf(item), 1);
            },
            info(msg) { this.show(msg, {type: 'primary', icon: 'info-circle'}); },
            success(msg) { this.show(msg, {type: 'success', icon: 'check-circle'}); },
            error(msg) { this.show(msg, {type: 'error', icon: 'ban'}); },
            warning(msg) { this.show(msg, {type: 'warning', icon: 'exclamation-circle'}); },
            alert(msg, title) { this.show(msg, {x: 'center', y: 'top', title: title || location.host, lifetime: 0, buttons: {OK: () => {}}}); },
        }
    });
})();