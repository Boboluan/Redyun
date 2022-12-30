(function () {
    class ZUI {
        constructor() {

        }

        paging(options) {
            new Paging(options)
        }

    }

    self.ZUI = new ZUI()


    function Paging(opt) {
        var xx = this
        xx.callBack = opt.callBack
        if (!opt.count || !opt.selector) {
            alert('参数count,selector必填!')
            return
        }
        xx.count = opt.count
        xx.elem = $(opt.selector)
        xx.page_len = opt.page_len
        xx.current = opt.current

        xx.insertElement()
    }

    Paging.prototype.createTemplate = function () {
        var xx = this
        var i, b = '',
            c, str, f = xx.page_len
        if (xx.count <= (f - 3)){
            i = 1
            while (i <= (f - 2) && i <= xx.count) {
                i == xx.current ? b += '<a href="javascript:;" data-page="curr" class="current">' + (i) + '</a>' : b += '<a href="javascript:;" class="num" data-page=' + (i) + '>' + (i) + '</a>'
                i++
            }
            str = `
                <a href="javascript:;" data-page="prev" class="prev">&lt;&lt;</a>
                ${b}
                <a href="javascript:;" data-page="next" class="next">&gt;&gt;</a>
                <input class="p_txt" type="text" id="page"/>
                <button class="p_btn" type="button" id="goBtn">GO</button>
            `
        }else if (f - xx.current <= 2 && xx.count - xx.current >= (f - 3)) {
            c = (f - 1 - 2) / 2,
                i = -c, str
            while (i <= c) {
                i == 0 ? b += '<a href="javascript:;" data-page="curr" class="current">' + (xx.current) + '</a>' : b += '<a class="num" data-page=' + (+xx.current + i) + ' >' + (+xx.current + i) + '</a>'
                i++
            }
            str = `
                <a href="javascript:;" data-page="prev" class="prev">&lt;&lt;</a>
                <a href="javascript:;" data-page="1">1</a>
                <a href="javascript:;" data-page="mid">…</a>
                ${b}
                <a href="javascript:;" data-page="mid">…</a>
                <a href="javascript:;" data-page="${xx.count}">${xx.count}</a>
                <a href="javascript:;" data-page="next" class="next">&gt;&gt;</a>
                <input class="p_txt" type="text" id="page"/>
                <button class="p_btn" type="button" id="goBtn">GO</button>
            `
        } else if (0 < xx.current && xx.current <= (f - 3)) {
            i = 1
            while (i <= (f - 2) && i < xx.count) {
                i == xx.current ? b += '<a href="javascript:;" data-page="curr" class="current">' + (i) + '</a>' : b += '<a class="num" href="javascript:;" data-page=' + (i) + '>' + (i) + '</a>'
                i++
            }
            str = `
                <a href="javascript:;" data-page="prev" class="prev">&lt;&lt;</a>
                ${b}
                <a href="javascript:;" data-page="mid">…</a>
                <a href="javascript:;" data-page="${xx.count}">${xx.count}</a>
                <a href="javascript:;" data-page="next" class="next">&gt;&gt;</a>
                <input class="p_txt" type="text" id="page"/>
                <button class="p_btn" type="button" id="goBtn">GO</button>
            `
        } else if ((xx.count - (f - 3)) < xx.current && xx.current <= xx.count) {
            i = (f - 2) - 1
            while (i >= 0) {
                xx.count - i == xx.current ? b += '<a href="javascript:;" class="current">' + (xx.count - i) + '</a>' : b += '<a class="num" href="javascript:;" data-page=' + (xx.count - i) + '>' + (xx.count - i) + '</a>'
                i--
            }
            str = `
                <a href="javascript:;" data-page="prev" class="prev">&lt;&lt;</a>
                <a href="javascript:;" data-page="1">1</a>
                <a href="javascript:;" data-page="mid">…</a>
                ${b}
                <a href="javascript:;" data-page="next" class="next">&gt;&gt;</a>
                <input class="p_txt" type="text" id="page"/>
                <button class="p_btn" type="button" id="goBtn">GO</button>
            `
        }
        return str
    }, Paging.prototype.insertElement = function () {
        var xx = this
        var str = this.createTemplate(),
            s = `
            <div class="paging_box">
                ${str}
            </div>
        `
        xx.elem.html(s)
        xx.pagingClick()
        xx.goToCurrent()
    }, Paging.prototype.pagingClick = function () {
        var xx = this
        xx.elem.on('click', 'a', function (e) {
            var page = $(this).attr('data-page')
            if (page != 'curr' && page != 'mid') {
                if (page == 'prev') {
                    xx.current > 1 ? xx.current-- : xx.current
                    xx.callBack(xx.current)
                    xx.elem.find('.paging_box').html(xx.createTemplate())
                } else if (page == 'next') {
                    xx.current < xx.count ? xx.current++ : xx.current
                    xx.callBack(xx.current)
                    xx.elem.find('.paging_box').html(xx.createTemplate())
                } else {
                    xx.callBack(page)
                    xx.current = page
                    xx.elem.find('.paging_box').html(xx.createTemplate())
                }
            }
        })
    }, Paging.prototype.goToCurrent = function () {
        var xx = this
        xx.elem.on('click', '#goBtn', function (e) {
            var page = $("#page").val();
            if (page < 1) {
                page = 1
            } else if (page > xx.count) {
                page = xx.count
            }
            xx.callBack(page)
            xx.current = page
            xx.elem.find('.paging_box').html(xx.createTemplate())
        })
    }
})(window)
