class Chairs {
    get shapeText() {
        return 'chairs';
    }
    get position() {
        return {
            top: this.top,
            left: this.left
        };
    }
    get div() {
        return this.holder;
    }
    get size() {
        return {
            width: this.totalWidth,
            length: this.totalLength,
            height: this.height
        };
    }
    get totalLength() {
        return (this.length * this.perRow) + ((this.perRow - 1) * this.between);
    }
    get totalWidth() {
        return (this.width * this.rows) + ((this.rows - 1) * this.aisle);
    }
    get snapshot() {
        var obj = {
            shape: ELEMENT_CHAIRS,
            name: this.name,
            length: this.length,
            width: this.width,
            height: this.height,
            radius: this.radius,
            color: this.color,
            top: this.top,
            left: this.left,
            layer: this.layer,
            safe: this.safe,
            aisle: this.aisle,
            between: this.between,
            perRow: this.perRow,
            rows: this.rows,
            showDims: this.showDims
        };
        return obj;
    }
    get getUniqueId() {
        return this.uniqueId;
    }
    constructor(...properties) {
        this.uniqueId = Math.floor(Math.random() * 1000000);
        this.name = properties[0] || '';
        this.length = properties[1] || 50;
        this.width = properties[2] || 50;
        this.height = properties[3] || 50;
        this.top = properties[4] || 0;
        this.left = properties[5] || 0;
        this.color = properties[6] || '#ffffff';
        this.safe = properties[7];
        this.aisle = properties[8] || 50;
        this.between = properties[9] || 10;
        this.perRow = properties[10] || 5;
        this.rows = properties[11] || 8;
        this.radius = properties[12] || 0;
        this.layer = properties[13] || 10;
        this.showDims = properties[14] || 0;
        this.create();
    }
    scale(calc) {
        var scl = ((this.totalLength / 7) + (this.totalWidth / 7)) / 2;
        this.holder.find('.title').css('font-size', calc(scl));
        this.holder.find('.box_height').css('width', calc(this.totalWidth));
        let smlr = Math.min(this.totalLength, this.totalWidth);
        let fntsize = smlr / 12;
        if (calc(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (calc(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.chair').css({
            'height': calc(this.width),
            'flex-basis': calc(this.length)
        });
        this.holder.find('.rowie:not(:first-child) .chair').css({
            'margin-top': calc(this.aisle)
        });
        this.holder.find('.chair:not(:last-child)').css({
            'margin-right': calc(this.between)
        });
        this.holder.css({
            'width': calc(this.calcSafe(this.totalLength)),
            'height': calc(this.calcSafe(this.totalWidth)),
            'top': calc(this.top),
            'left': calc(this.left),
            'padding': calc(this.safe)
        });
    }
    changeShowDims(show) {
        this.showDims = parseInt(show);
        if (this.showDims == 0) {
            this.holder.find('.box_width').addClass('hdn');
            this.holder.find('.box_height').addClass('hdn');
        }
        else {
            this.holder.find('.box_width').removeClass('hdn');
            this.holder.find('.box_height').removeClass('hdn');
        }
    }
    changePosition(top, left) {
        this.top = parseInt(top);
        this.left = parseInt(left);
        this.holder.css('top', world.scaleValue(this.calcSafe(this.top)));
        this.holder.css('left', world.scaleValue(this.calcSafe(this.left)));
    }
    changeName(name) {
        this.name = name;
        this.holder.find('.title span').text(name);
    }
    changeLength(length) {
        this.length = parseInt(length);
        this.holder.find('.chair').css({
            'flex-basis': world.scaleValue(this.length)
        });
        this.holder.css({
            'width': world.scaleValue(this.calcSafe(this.totalLength))
        });
        this.holder.find('.box_width span').text(cm2m((this.length * this.perRow) + (this.between * (this.perRow - 1))));
        this.resize();
    }
    changeSafe(safe) {
        this.safe = parseInt(safe);
        this.holder.css({
            'padding': world.scaleValue(safe),
            'width': world.scaleValue(this.calcSafe(this.totalLength)),
            'height': world.scaleValue(this.calcSafe(this.totalWidth)),
        });
        this.resize();
    }
    changeWidth(width) {
        this.width = parseInt(width);
        this.holder.find('.chair').css({
            'height': world.scaleValue(this.width)
        });
        this.holder.css({
            'height': world.scaleValue(this.calcSafe(this.totalWidth))
        });
        this.holder.find('.box_height span').text(cm2m((this.width * this.rows) + (this.aisle * (this.rows - 1))));
        this.resize();
    }
    changeAisle(aisle) {
        this.aisle = parseInt(aisle);
        this.holder.find('.rowie:not(:first-child) .chair').css({
            'margin-top': world.scaleValue(this.aisle)
        });
        this.holder.css({
            'height': world.scaleValue(this.calcSafe(this.totalWidth))
        });
        this.resize();
    }
    changeBetween(between) {
        this.between = parseInt(between);
        this.holder.find('.chair:not(:last-child)').css({
            'margin-right': world.scaleValue(this.between)
        });
        this.holder.css({
            'width': world.scaleValue(this.calcSafe(this.totalLength))
        });
        this.resize();
    }
    resize() {
        var scl = ((this.totalLength / 7) + (this.totalWidth / 7)) / 2;
        this.holder.find('.title').css('font-size', world.scaleValue(scl));
        this.holder.find('.box_height').css('width', world.scaleValue(this.totalWidth));
        let smlr = Math.min(this.totalLength, this.totalWidth);
        let fntsize = smlr / 12;
        if (world.scaleValue(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (world.scaleValue(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
    }
    changeRows(rows) {
        this.rows = rows;
        this.rebuild();
    }
    changePerRow(perRow) {
        this.perRow = perRow;
        this.rebuild();
    }
    rebuild() {
        var $chairs = this.holder.find('.chairs').text('');
        for (var i = 0; i < this.rows; i++) {
            var $row = jQuery('<div>', {
                class: 'rowie'
            }).appendTo($chairs);
            for (var j = 0; j < this.perRow; j++)
                $row.append(jQuery('<div>', {
                    class: 'chair'
                }).css({
                    'background-color': this.color,
                    'height': world.scaleValue(this.width),
                    'flex-basis': world.scaleValue(this.length)
                }));
        }
        $chairs.find('.rowie:not(:first-child) .chair').css({
            'margin-top': world.scaleValue(this.aisle)
        });
        $chairs.find('.chair:not(:last-child)').css({
            'margin-right': world.scaleValue(this.between)
        });
        this.holder.css({
            'height': world.scaleValue(this.calcSafe(this.totalWidth))
        });
        this.holder.css({
            'width': world.scaleValue(this.calcSafe(this.totalLength))
        });
        this.resize();
    }
    changeColor(color) {
        this.color = color;
        this.holder.find('.chair').css('background-color', this.color);
    }
    changeHeight(height) {
        this.height = parseInt(height);
    }
    changeLayer(value) {
        var newLayer = this.layer + parseInt(value);
        if (newLayer >= 10) {
            this.layer = newLayer;
            this.holder.css('z-index', newLayer);
        }
    }
    changeRadius(value) {
        this.radius = value;
        this.holder.css({
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)'
        });
    }
    remove() {
        world.map.space.removeElmnt(this.uniqueId);
        this.holder.remove();
    }
    calcSafe(value) {
        return parseInt(value + '') + (2 * this.safe);
    }
    create() {
        var parent = this;
        this.holder = jQuery('<div>', {
            class: "obj",
        }).css({
            'width': world.scaleValue(this.calcSafe(this.totalLength)),
            'height': world.scaleValue(this.calcSafe(this.totalWidth)),
            'left': world.scaleValue(this.left),
            'top': world.scaleValue(this.top),
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)',
            'z-index': this.layer,
            'padding': world.scaleValue(this.safe)
        })
            .append(jQuery('<div>', {
            class: 'core'
        })
            .append(jQuery('<div>', {
            class: 'objhandle'
        }).on('click', function (evt) {
            var $obj = $(this).closest('.obj');
            var state = $obj.hasClass('slcted');
            evt.ctrlKey ? null : $('.obj').removeClass('slcted');
            state ? $obj.removeClass('slcted') : $obj.addClass('slcted');
        }), jQuery('<div>', {
            class: 'chairs'
        }), jQuery('<div>', {
            class: "box_width disable-select" + (!this.showDims ? ' hdn' : '')
        }).append(jQuery('<span>', {
            text: cm2m((this.length * this.perRow) + (this.between * (this.perRow - 1)))
        })), jQuery('<div>', {
            class: "box_height disable-select" + (!this.showDims ? ' hdn' : '')
        }).append(jQuery('<span>', {
            text: cm2m((this.width * this.rows) + (this.aisle * (this.rows - 1)))
        })), jQuery('<div>', {
            class: "title disable-select"
        }).append(jQuery('<span>', {
            text: this.name
        })).css('font-size', world.scaleValue(30)), jQuery('<span>', {
            class: "removeme",
            html: '<i class="fa fa-remove" aria-hidden="true"></i>',
            title: "Usuń element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            $.confirm({
                title: 'Potwierdzenie usunięcia',
                content: 'Czy aby na pewno chcesz to zrobić?',
                buttons: {
                    'Usuń': function () {
                        parent.remove();
                    },
                    'Anuluj': function () {
                        return;
                    }
                }
            });
        }), jQuery('<span>', {
            class: "editme",
            html: '<i class="fa fa-pencil" aria-hidden="true"></i>',
            title: "Edytuj element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            world.elmntForEdit = parent;
            $('.obj').removeClass('editl');
            parent.holder.addClass('editl');
            $('.editobj form .col').css('display', 'none');
            $('.editobj form .col.chairs').css('display', 'block');
            $('.editobj .chairs input#objname').val(parent.name).data('original', parent.name);
            $('.editobj .chairs input#objlength').val(parent.length).data('original', parent.length);
            $('.editobj .chairs input#objwidth').val(parent.width).data('original', parent.width);
            $('.editobj .chairs input.objheight').val(parent.height).data('original', parent.height);
            $('.editobj .chairs input#objradius').val(parent.radius).data('original', parent.radius);
            $('.editobj .chairs input#objcolor').val(parent.color).data('original', parent.color);
            $('.editobj .chairs input#objsafe').val(parent.safe).data('original', parent.safe);
            $('.editobj .chairs input#objrows').val(parent.rows).data('original', parent.rows);
            $('.editobj .chairs input#objperrow').val(parent.perRow).data('original', parent.perRow);
            $('.editobj .chairs input#objaisle').val(parent.aisle).data('original', parent.aisle);
            $('.editobj .chairs input#objbetween').val(parent.between).data('original', parent.between);
            $('.editobj .chairs input#objshowdims').prop('checked', parent.showDims > 0).data('original', parent.showDims > 0);
            $('.menuView2').css('display', 'none');
            $('.menuView8').css('display', 'none');
            $('.menuView9').css('display', 'none');
            $('.menuView10').css('display', 'none');
            $('.menuView3').css('display', 'none');
            $('.menuView4').css('display', 'block');
        }), jQuery('<span>', {
            class: "rotateleft",
            html: '<i class="fa fa-rotate-left" aria-hidden="true"></i>',
            title: "Obróć w lewo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius - 1);
        }), jQuery('<span>', {
            class: "rotateright",
            html: '<i class="fa fa-rotate-right" aria-hidden="true"></i>',
            title: "Obróć w prawo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius + 1);
        }), jQuery('<span>', {
            class: "copyme",
            html: '<i class="fa fa-clone" aria-hidden="true"></i>',
            title: "Duplikuj",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            var snapshot = parent.snapshot;
            world.map.space.addElmnt(null, snapshot);
        }))).draggable({
            scroll: false,
            containment: ".map",
            cancel: '.title',
            stop: function () {
                var myPos = $(this).offset();
                var paPos = $(this).parent().offset();
                parent.top = world.realViewScale(myPos.top - paPos.top);
                parent.left = world.realViewScale(myPos.left - paPos.left);
            },
            drag: function (e, ui) {
                if (shiftIsPressed) {
                    ui.helper.clone().addClass('clonie').appendTo('.space').data('snapshot', parent.snapshot);
                    e.preventDefault();
                    return false;
                }
            }
        }).appendTo('.space');
        var $chairs = this.holder.find('.chairs');
        for (var i = 0; i < this.rows; i++) {
            var $row = jQuery('<div>', {
                class: 'rowie'
            }).appendTo($chairs);
            for (var j = 0; j < this.perRow; j++)
                $row.append(jQuery('<div>', {
                    class: 'chair'
                }).css({
                    'background-color': this.color,
                    'height': world.scaleValue(this.width),
                    'flex-basis': world.scaleValue(this.length)
                }));
        }
        $chairs.find('.rowie:not(:first-child) .chair').css({
            'margin-top': world.scaleValue(this.aisle)
        });
        $chairs.find('.chair:not(:last-child)').css({
            'margin-right': world.scaleValue(this.between)
        });
        this.resize();
    }
}
class Circle {
    get shapeText() {
        return 'circle';
    }
    get position() {
        return {
            top: this.top,
            left: this.left
        };
    }
    get div() {
        return this.holder;
    }
    get snapshot() {
        var obj = {
            name: this.name,
            shape: ELEMENT_CIRCLES,
            fi: this.fi,
            height: this.height,
            radius: this.radius,
            color: this.color,
            top: this.top,
            left: this.left,
            layer: this.layer,
            safe: this.safe,
            showDims: this.showDims,
            aisle: this.aisle,
            between: this.between,
            perRow: this.perRow,
            rows: this.rows
        };
        return obj;
    }
    get totalLength() {
        return (this.fi * this.perRow) + ((this.perRow - 1) * this.between) + (2 * this.safe * this.perRow);
    }
    get totalWidth() {
        return (this.fi * this.rows) + ((this.rows - 1) * this.aisle) + (2 * this.safe * this.rows);
    }
    get size() {
        return {
            width: this.totalWidth,
            length: this.totalLength,
            height: this.height
        };
    }
    get getUniqueId() {
        return this.uniqueId;
    }
    constructor(...properties) {
        this.uniqueId = Math.floor(Math.random() * 1000000);
        this.name = properties[0] || '';
        this.fi = properties[1] || 100;
        this.height = properties[2] || 100;
        this.top = properties[3] || 0;
        this.left = properties[4] || 0;
        this.color = properties[5] || '#ffffff';
        this.safe = properties[6];
        this.aisle = properties[7] || 50;
        this.between = properties[8] || 50;
        this.perRow = properties[9] || 2;
        this.rows = properties[10] || 2;
        this.radius = properties[11] || 0;
        this.layer = properties[12] || 10;
        this.showDims = properties[13] || 0;
        this.create();
    }
    scale(calc) {
        let smlr = Math.min(this.totalLength, this.totalWidth);
        let fntsize = smlr / 12;
        if (calc(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (calc(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.rowie:not(:first-child) .circle-safe').css({
            'margin-top': calc(this.aisle)
        });
        this.holder.find('.circle-safe:not(:last-child)').css({
            'margin-right': calc(this.between)
        });
        this.holder.css({
            'width': calc(this.totalLength),
            'height': calc(this.totalWidth),
            'top': calc(this.top),
            'left': calc(this.left)
        });
        this.holder.find('.circle-safe').css({
            'flex-basis': calc(this.fi + (2 * this.safe)),
            'height': calc(this.fi + (2 * this.safe)),
            'padding-left': calc(this.safe),
            'padding-top': calc(this.safe)
        });
        this.holder.find('.circle').css({
            'width': calc(this.fi),
            'height': calc(this.fi)
        });
        this.holder.find('.circle > div').css({
            'width': calc(this.fi),
            'height': calc(this.fi)
        });
        var scl = ((this.totalLength / 7) + (this.totalWidth / 7)) / 2;
        this.holder.find('.title').css('font-size', calc(scl));
        this.holder.find('.box_height').css('width', calc(this.totalWidth));
    }
    rebuild() {
        var $circles = this.holder.find('.circles').text('');
        for (var i = 0; i < this.rows; i++) {
            var $row = jQuery('<div>', {
                class: 'rowie'
            }).appendTo($circles);
            for (var j = 0; j < this.perRow; j++) {
                var $circle = jQuery('<div>', {
                    class: 'circle-safe'
                }).append(jQuery('<div>', {
                    class: 'circle'
                }).css({
                    'width': world.scaleValue(this.fi),
                    'height': world.scaleValue(this.fi)
                })).appendTo($row);
            }
        }
        this.changeSafe();
        this.changeColor();
        this.changeAisle();
        this.changeBetween();
        this.resize();
    }
    changeShowDims(show) {
        this.showDims = parseInt(show);
        if (this.showDims == 0) {
            this.holder.find('.box_width').addClass('hdn');
            this.holder.find('.box_height').addClass('hdn');
        }
        else {
            this.holder.find('.box_width').removeClass('hdn');
            this.holder.find('.box_height').removeClass('hdn');
        }
    }
    changePosition(top, left) {
        this.top = parseInt(top);
        this.left = parseInt(left);
        this.holder.css('top', world.scaleValue(this.calcSafe(this.top)));
        this.holder.css('left', world.scaleValue(this.calcSafe(this.left)));
    }
    changeName(name) {
        this.name = name;
        this.holder.find('.title span').text(name);
    }
    changeFi(fi) {
        if (fi != null) {
            this.fi = parseInt(fi);
            this.resize();
        }
        this.holder.find('.circle').css({
            'width': world.scaleValue(this.fi),
            'height': world.scaleValue(this.fi)
        });
        this.changeSafe();
        this.holder.find('.circle > .box_width span').text(cm2m(this.fi));
        this.holder.find('.circle > .box_height span').text(cm2m(this.fi));
    }
    changeRadius(value) {
        this.radius = parseInt(value);
        this.holder.css({
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)'
        });
    }
    changeSafe(safe = null) {
        if (safe != null) {
            this.safe = parseInt(safe);
            this.resize();
        }
        console.log(this.safe, this.fi);
        this.holder.find('.circle-safe').css({
            'flex-basis': world.scaleValue(this.fi + (2 * this.safe)),
            'height': world.scaleValue(this.fi + (2 * this.safe)),
            'padding-left': world.scaleValue(this.safe),
            'padding-top': world.scaleValue(this.safe)
        });
    }
    changeColor(color = null) {
        if (color != null) {
            this.color = color;
        }
        this.holder.find('.circle').css('background-color', this.color);
    }
    changeHeight(height) {
        this.height = parseInt(height);
    }
    changeLayer(value) {
        var newLayer = this.layer + parseInt(value);
        if (newLayer >= 10) {
            this.layer = newLayer;
            this.holder.css('z-index', newLayer);
        }
    }
    remove() {
        world.map.space.removeElmnt(this.uniqueId);
        this.holder.remove();
    }
    changeAisle(aisle = null) {
        if (aisle != null) {
            this.aisle = parseInt(aisle);
            this.resize();
        }
        this.holder.find('.rowie:not(:first-child) .circle-safe').css({
            'margin-top': world.scaleValue(this.aisle)
        });
    }
    changeBetween(between = null) {
        if (between != null) {
            this.between = parseInt(between);
            this.resize();
        }
        this.holder.find('.circle-safe:not(:last-child)').css({
            'margin-right': world.scaleValue(this.between)
        });
    }
    resize() {
        this.holder.css({
            'width': world.scaleValue(this.totalLength),
            'height': world.scaleValue(this.totalWidth)
        });
        this.holder.find('.box_height').css('width', world.scaleValue(this.totalWidth));
        let smlr = Math.min(this.totalLength, this.totalWidth);
        let fntsize = smlr / 12;
        if (world.scaleValue(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (world.scaleValue(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
        var scl = ((this.totalLength / 7) + (this.totalWidth / 7)) / 2;
        this.holder.find('.title').css('font-size', world.scaleValue(scl));
        this.holder.find('.core > .box_width span').text(cm2m(this.totalLength));
        this.holder.find('.core > .box_height span').text(cm2m(this.totalWidth));
    }
    changeRows(rows) {
        this.rows = parseInt(rows);
        this.rebuild();
    }
    changePerRow(perRow) {
        this.perRow = parseInt(perRow);
        this.rebuild();
    }
    calcSafe(value) {
        return parseInt(value) + (2 * this.safe);
    }
    create() {
        var parent = this;
        this.holder = jQuery('<div>', {
            class: "obj t",
        }).css({
            'width': world.scaleValue(parent.totalLength),
            'height': world.scaleValue(parent.totalWidth),
            'left': world.scaleValue(this.left),
            'top': world.scaleValue(this.top),
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)',
            'z-index': this.layer
        }).append(jQuery('<div>', {
            class: 'core'
        }).css({
            'border': 'none'
        }).append(jQuery('<div>', {
            class: 'objhandle'
        }).on('click', function (evt) {
            var $obj = $(this).closest('.obj');
            var state = $obj.hasClass('slcted');
            evt.ctrlKey ? null : $('.obj').removeClass('slcted');
            state ? $obj.removeClass('slcted') : $obj.addClass('slcted');
        }), jQuery('<div>', {
            class: 'circles'
        }), jQuery('<div>', {
            class: "box_width disable-select" + (!this.showDims ? ' hdn' : '')
        }).append(jQuery('<span>', {
            text: cm2m(this.totalLength)
        })), jQuery('<div>', {
            class: "box_height disable-select" + (!this.showDims ? ' hdn' : '')
        }).append(jQuery('<span>', {
            text: cm2m(this.totalWidth)
        })), jQuery('<div>', {
            class: "title disable-select"
        }).append(jQuery('<span>', {
            text: this.name
        })).css('font-size', world.scaleValue(20)), jQuery('<span>', {
            class: "removeme",
            html: '<i class="fa fa-remove" aria-hidden="true"></i>',
            title: "Usuń element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            $.confirm({
                title: 'Potwierdzenie usunięcia',
                content: 'Czy aby na pewno chcesz to zrobić?',
                buttons: {
                    'Usuń': function () {
                        parent.remove();
                    },
                    'Anuluj': function () {
                        return;
                    }
                }
            });
        }), jQuery('<span>', {
            class: "editme",
            html: '<i class="fa fa-pencil" aria-hidden="true"></i>',
            title: "Edytuj element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            world.elmntForEdit = parent;
            $('.obj').removeClass('editl');
            parent.holder.addClass('editl');
            $('.editobj form .col').css('display', 'none');
            $('.editobj form .col.circles').css('display', 'block');
            $('.editobj .circles input#objname').val(parent.name).data('original', parent.name);
            $('.editobj .circles input#objfi').val(parent.fi).data('original', parent.fi);
            $('.editobj .circles input.objheight').val(parent.height).data('original', parent.height);
            $('.editobj .circles input#objcolor').val(parent.color).data('original', parent.color);
            $('.editobj .circles input#objsafe').val(parent.safe).data('original', parent.safe);
            $('.editobj .circles input#objshowdims').prop('checked', parent.showDims > 0).data('original', parent.showDims > 0);
            $('.editobj .circles input#objrows').val(parent.rows).data('original', parent.rows);
            $('.editobj .circles input#objperrow').val(parent.perRow).data('original', parent.perRow);
            $('.editobj .circles input#objaisle').val(parent.aisle).data('original', parent.aisle);
            $('.editobj .circles input#objbetween').val(parent.between).data('original', parent.between);
            $('.menuView2').css('display', 'none');
            $('.menuView8').css('display', 'none');
            $('.menuView9').css('display', 'none');
            $('.menuView10').css('display', 'none');
            $('.menuView3').css('display', 'none');
            $('.menuView4').css('display', 'block');
        }), jQuery('<span>', {
            class: "rotateleft",
            html: '<i class="fa fa-rotate-left" aria-hidden="true"></i>',
            title: "Obróć w lewo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius - 1);
        }), jQuery('<span>', {
            class: "rotateright",
            html: '<i class="fa fa-rotate-right" aria-hidden="true"></i>',
            title: "Obróć w prawo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius + 1);
        }), jQuery('<span>', {
            class: "copyme",
            html: '<i class="fa fa-clone" aria-hidden="true"></i>',
            title: "Duplikuj",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            var snapshot = parent.snapshot;
            world.map.space.addElmnt(null, snapshot);
        }))).draggable({
            scroll: false,
            containment: ".map",
            cancel: '.title',
            stop: function () {
                var myPos = $(this).offset();
                var paPos = $(this).parent().offset();
                parent.top = world.realViewScale(myPos.top - paPos.top);
                parent.left = world.realViewScale(myPos.left - paPos.left);
            },
            drag: function (e, ui) {
                if (shiftIsPressed) {
                    ui.helper.clone().addClass('clonie').appendTo('.space').data('snapshot', parent.snapshot);
                    e.preventDefault();
                    return false;
                }
            }
        }).appendTo('.space');
        this.rebuild();
    }
}
class Elmnt {
    set name(name) {
        this.variant.changeName(name);
    }
    set length(length) {
        this.variant.changeLength(length);
    }
    set safe(safe) {
        this.variant.changeSafe(safe);
    }
    set width(width) {
        this.variant.changeWidth(width);
    }
    set dims(state) {
        this.variant.changeShowDims(state);
    }
    set color(color) {
        this.variant.changeColor(color);
    }
    set height(height) {
        this.variant.changeHeight(height);
    }
    set rows(rows) {
        this.variant.changeRows(rows);
    }
    set perRow(perRow) {
        this.variant.changePerRow(perRow);
    }
    set aisle(aisle) {
        this.variant.changeAisle(aisle);
    }
    get id() {
        return this.variant.getUniqueId;
    }
    get snapshot() {
        return this.variant.snapshot;
    }
    layer(layer) {
        this.variant.changeLayer(layer);
    }
    rotate(radius) {
        this.variant.changeRadius(radius);
    }
    get position() {
        var snap = this.variant.snapshot;
        return {
            top: snap.top,
            left: snap.left
        };
    }
    constructor(shape, ...properties) {
        switch (shape) {
            case ELEMENT_RECTANGLE: {
                this.variant = new Rectangle(...properties);
                break;
            }
            case ELEMENT_CIRCLES: {
                this.variant = new Circle(...properties);
                break;
            }
            case ELEMENT_CHAIRS: {
                this.variant = new Chairs(...properties);
                break;
            }
            case ELEMENT_TABLES: {
                this.variant = new Tables(...properties);
                break;
            }
        }
    }
    scale(calc) {
        this.variant.scale(calc);
    }
    remove() {
        $('.menuView2').css('display', 'block');
        if (world.map.space.hasImage()) {
            $('.menuView10').css('display', 'block');
        }
        else {
            $('.menuView9').css('display', 'block');
        }
        $('.menuView2').css('display', 'block');
        $('.menuView3').css('display', 'none');
        $('.menuView4').css('display', 'none');
        world.elmntForEdit = null;
    }
}
class Line {
    get div() {
        return this.holder;
    }
    set temp(stat) {
        this.temporary = stat;
    }
    get space() {
        return world.map.space.div;
    }
    get id() {
        return this.uniqueId;
    }
    get snapshot() {
        var obj = {
            top: this.top,
            left: this.left,
            x1: this.x1,
            y1: this.y1,
            x2: this.x2,
            y2: this.y2,
            height: this.height,
            width: this.width
        };
        return obj;
    }
    constructor(x1, y1, x2, y2, width = null, height = null) {
        this.uniqueId = Math.floor(Math.random() * 1000000);
        this.x1 = x1;
        this.y1 = y1;
        this.x2 = x2 === null ? x1 : x2;
        this.y2 = y2 === null ? y1 : y2;
        this.width = width;
        this.height = height;
        this.temporary = false;
        this.createLine();
    }
    mouse(x2, y2) {
        x2 = world.realViewScale(x2);
        y2 = world.realViewScale(y2);
        if (shiftIsPressed) {
            let vX = x2 - this.x1;
            let vY = y2 - this.y1;
            if (vY < vX && vY < -vX || vY > vX && vY > -vX) {
                x2 = this.x1;
            }
            else if (vY > vX && vY < -vX || vY < vX && vY > -vX) {
                y2 = this.y1;
            }
        }
        this.x2 = x2;
        this.y2 = y2;
        var parent = this;
        this.holder.redraw(world.scaleValue(parent.x1), world.scaleValue(parent.y1), world.scaleValue(parent.x2), world.scaleValue(parent.y2), {
            height: world.scaleValue(parent.height)
        }, (line) => {
            let width = parseFloat($(line).css('width').replace('px', ''));
            let top = parseFloat($(line).css('top').replace('px', ''));
            let left = parseFloat($(line).css('left').replace('px', ''));
            parent.width = world.realViewScale(width);
            parent.top = world.realViewScale(top);
            parent.left = world.realViewScale(left);
            parent.holder.find('.box_width span').text(cm2m(parent.width));
        });
        this.holder;
    }
    remove() {
        this.holder.remove();
    }
    createLine() {
        var parent = this;
        $('.space').line(world.scaleValue(parent.x1), world.scaleValue(parent.y1), world.scaleValue(parent.x2), world.scaleValue(parent.y2), {
            height: world.scaleValue(parent.height)
        }, (line) => {
            parent.holder = $(line);
            parent.holder.append(jQuery('<div>', {
                class: "box_width disable-select"
            }).append(jQuery('<span>', {
                text: cm2m(parent.width)
            })), jQuery('<span>', {
                class: "removeme",
                html: '<i class="fa fa-remove" aria-hidden="true"></i>'
            }).on('click', function () {
                world.map.space.removeLine(parent.uniqueId);
            }), jQuery('<div>', {
                class: 'line-arr-right'
            }).append(jQuery('<span>', {
                class: 'fa fa-caret-right'
            })), jQuery('<div>', {
                class: 'line-arr-left'
            }).append(jQuery('<span>', {
                class: 'fa fa-caret-left'
            })));
            let width = parseFloat($(line).css('width').replace('px', ''));
            let height = parseFloat($(line).css('height').replace('px', ''));
            let top = parseFloat($(line).css('top').replace('px', ''));
            let left = parseFloat($(line).css('left').replace('px', ''));
            parent.width = world.realViewScale(width);
            parent.height = height;
            parent.top = world.realViewScale(top);
            parent.left = world.realViewScale(left);
        });
        parent.holder.on('click', (evt) => {
            if (!parent.temporary) {
                var state = parent.holder.hasClass('slcted');
                evt.ctrlKey ? null : $('.line').removeClass('slcted');
                state ? parent.holder.removeClass('slcted') : parent.holder.addClass('slcted');
            }
        });
    }
    scale(calc) {
        this.holder.css({
            'width': calc(this.width),
            'height': calc(this.height),
            'top': calc(this.top),
            'left': calc(this.left)
        });
    }
}
const MAPMOVESTEP = 10;
const VIEWSCALESTEP = 10;
const CMPERM = 100;
const ELEMENT_RECTANGLE = 1;
const ELEMENT_CIRCLES = 2;
const ELEMENT_CHAIRS = 3;
const ELEMENT_TABLES = 4;
var world = null;
var cropper;
var lineMode = 0;
var shiftIsPressed = false;
var isMouseDown = false;
var showDims = 0;
if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match;
        });
    };
}
$(() => {
    $(document).mousedown(function () {
        isMouseDown = true;
    }).mouseup(function () {
        isMouseDown = false;
    });
    $('.ctrls button').removeClass('btn-success').addClass('disabled');
    $('#toolbar a.nav-link').addClass('disabled');
    if (typeof (Storage) !== "undefined") {
        let backup = localStorage.getItem('backup');
        if (backup) {
            let backup = JSON.parse(localStorage.getItem('backup'));
            let userid = Cookies.get('userid');
            if (userid && userid === backup.userid)
                $.confirm({
                    title: 'Odzyskiwanie projektu',
                    content: 'Ostatnio edytowany projekt nie został zapisany przed wyjściem. Chcesz go teraz przywrócić?',
                    buttons: {
                        'Przywróć': () => {
                            let backup = JSON.parse(localStorage.getItem('backup'));
                            if (world != null) {
                                world.remove();
                            }
                            world = new World(backup.world.snapshot.projectName, backup.world.snapshot.projectDesc, backup.world.snapshot.projectId, backup.world.map);
                            $('#addworldhere').prepend(world.div);
                            $('.sidebar-current-project').text('')
                                .append(jQuery('<h6>', {
                                text: 'Projekt: ' + world.name
                            }));
                            $('.menuView6').css('display', 'none');
                            $('.menuView5').css('display', 'none');
                            $('.menuView2').css('display', 'block');
                            $('.ctrls button').removeClass('disabled');
                            $('#toolbar a.nav-link').removeClass('disabled');
                            if (backup.world.map.space.image) {
                                $('.menuView10').css('display', 'block');
                            }
                            else {
                                $('.menuView9').css('display', 'block');
                            }
                            localStorage.removeItem("backup");
                        },
                        'Usuń': () => {
                            localStorage.removeItem("backup");
                        }
                    }
                });
        }
    }
    $('.modal').modal({
        backdrop: 'static',
        show: false
    });
    $('#cropphotoaccept').on('click', function (e) {
        e.preventDefault();
        var $cropLength = $('#cropphoto #cropLength');
        var $cropWidth = $('#cropphoto #cropWidth');
        var $cropHeight = $('#cropphoto #cropHeight');
        if (!($cropLength.val()) && !($cropWidth.val())) {
            return;
        }
        if (!($cropHeight.val())) {
            return;
        }
        var cropBox = cropper.getCropBoxData();
        if (cropBox.width > 0 && cropBox.height > 0) {
            var imageData = cropper.getImageData();
            var spaceLength = 0;
            var spaceWidth = 0;
            if ($cropLength.val()) {
                spaceLength = (imageData.width * $cropLength.val()) / cropBox.width;
            }
            if ($cropWidth.val()) {
                spaceWidth = (imageData.height * $cropWidth.val()) / cropBox.height;
            }
            if (!$cropLength.val()) {
                spaceLength = (spaceWidth / imageData.height) * imageData.width;
            }
            if (!$cropWidth.val()) {
                spaceWidth = (spaceLength / imageData.width) * imageData.height;
            }
            var spaceHeight = $cropHeight.val();
            let space = world.map.space;
            if (space) {
                space.changeLength(spaceLength * CMPERM);
                space.changeWidth(spaceWidth * CMPERM);
                space.changeHeight(spaceHeight * CMPERM);
                space.changePhoto($('img#roomphoto').prop('src'));
                world.adjust();
            }
            else {
                world.map.createPhotoSpace($('img#roomphoto').prop('src'), spaceLength * CMPERM, spaceWidth * CMPERM, spaceHeight * CMPERM);
                $('#addworldhere').prepend(world.div);
                $('.ctrls button').removeClass('disabled');
                $('#toolbar a.nav-link').removeClass('disabled');
            }
            $('.menuView1').css('display', 'none');
            $('.menuView2').css('display', 'block');
            $('.menuView10').css('display', 'block');
            $('#cropphoto').modal('hide');
        }
    });
    $('#objadd #objaddaccept').on('click', function () {
        var name = $('#objadd input#objName').val();
        var shape = parseInt($('#objadd input#objShape').val());
        var top = parseInt($('#objadd input#objTop').val());
        var left = parseInt($('#objadd input#objLeft').val());
        var safe = parseInt($('#objadd input#objSafe').val());
        var color = $('#objadd input#objColor').val();
        var height = parseInt($('#objadd input#objHeight').val());
        let space = world.map.space.size;
        let errors = $('#objadd .alert ol').text('');
        if (height > space.height) {
            jQuery('<li>', {
                html: 'Wys. elementu <b>(' + cm2m(height) + ')</b> jest większa niż wys. pomieszczenia <b>(' + cm2m(space.height / CMPERM) + ')</b>.'
            }).appendTo(errors);
        }
        else {
            var elmnt = null;
            switch (shape) {
                case ELEMENT_RECTANGLE: {
                    var length = parseInt($('#objadd .rect input#objLength').val());
                    var width = parseInt($('#objadd .rect input#objWidth').val());
                    var forceShowHeight = parseInt($('#objadd input#objForceShowHeight').val());
                    let totalLength = length + (2 * safe);
                    if (totalLength > space.length) {
                        jQuery('<li>', {
                            html: 'Dł. całk. elementu <b>(' + cm2m(totalLength) + ')</b> jest większa niż dł. pomieszczenia <b>(' + cm2m(space.length / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    let totalWidth = width + (2 * safe);
                    if (totalWidth > space.width) {
                        jQuery('<li>', {
                            html: 'Sz. całk. elementu <b>(' + cm2m(totalWidth) + ')</b> jest większa niż sz. pomieszczenia <b>(' + cm2m(space.width / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    elmnt = new Elmnt(shape, name, length, width, height, top, left, color, safe, null, null, showDims, forceShowHeight);
                    break;
                }
                case ELEMENT_CIRCLES: {
                    var fi = parseInt($('#objadd .circles input#objFi').val());
                    var aisle = parseInt($('#objadd .circles input#objAisle').val());
                    var perRow = parseInt($('#objadd .circles input#objPerRow').val());
                    var rows = parseInt($('#objadd .circles input#objRows').val());
                    var between = parseInt($('#objadd .circles input#objBetween').val());
                    let totalLength = (fi * perRow) + (between * (perRow - 1)) + 100 + (2 * safe);
                    if (totalLength > space.length) {
                        jQuery('<li>', {
                            html: 'Dł. całk. elementu <b>(' + cm2m(totalLength) + ')</b> jest większa niż dł. pomieszczenia <b>(' + cm2m(space.length / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    let totalWidth = fi + (2 * safe);
                    if (totalWidth > space.width) {
                        jQuery('<li>', {
                            html: 'Sz. całk. elementu <b>(' + cm2m(totalWidth) + ')</b> jest większa niż sz. pomieszczenia <b>(' + cm2m(space.width / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    elmnt = new Elmnt(shape, name, fi, height, top, left, color, safe, aisle, between, perRow, rows, null, null, showDims);
                    break;
                }
                case ELEMENT_CHAIRS: {
                    var length = parseInt($('#objadd .chairs input#objLength').val());
                    var width = parseInt($('#objadd .chairs input#objWidth').val());
                    var aisle = parseInt($('#objadd .chairs input#objAisle').val());
                    var perRow = parseInt($('#objadd .chairs input#objPerRow').val());
                    var rows = parseInt($('#objadd .chairs input#objRows').val());
                    var between = parseInt($('#objadd .chairs input#objBetween').val());
                    let totalLength = (fi * perRow) + (between * (perRow - 1)) + (2 * safe);
                    if (totalLength > space.length) {
                        jQuery('<li>', {
                            html: 'Dł. całk. elementu <b>(' + cm2m(totalLength) + ')</b> jest większa niż dł. pomieszczenia <b>(' + cm2m(space.length / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    let totalWidth = (fi * rows) + (aisle * (rows - 1)) + (2 * safe);
                    if (totalWidth > space.width) {
                        jQuery('<li>', {
                            html: 'Sz. całk. elementu <b > (' + cm2m(totalWidth) + ')</b> jest większa niż sz. pomieszczenia <b > (' + cm2m(space.width / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    elmnt = new Elmnt(shape, name, length, width, height, top, left, color, safe, aisle, between, perRow, rows, null, showDims);
                    break;
                }
                case ELEMENT_TABLES: {
                    var fi = parseInt($('#objadd .tables #fis label.active').data('value'));
                    var aisle = parseInt($('#objadd .tables input#objAisle').val());
                    var perRow = parseInt($('#objadd .tables input#objPerRow').val());
                    var rows = parseInt($('#objadd .tables input#objRows').val());
                    var between = parseInt($('#objadd .tables input#objBetween').val());
                    var chairs = parseInt($('#objadd .tables .objchrs:checked').data('value'));
                    let totalLength = (fi * perRow) + (between * (perRow - 1)) + 100 + (2 * safe);
                    if (totalLength > space.length) {
                        jQuery('<li>', {
                            html: 'Dł. całk. elementu <b>(' + cm2m(totalLength) + ')</b> jest większa niż dł. pomieszczenia <b>(' + cm2m(space.length / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    let totalWidth = (fi * rows) + (aisle * (rows - 1)) + 100 + (2 * safe);
                    if (totalWidth > space.width) {
                        jQuery('<li>', {
                            html: 'Sz. całk. elementu <b > (' + cm2m(totalWidth) + ')</b> jest większa niż sz. pomieszczenia <b > (' + cm2m(space.width / CMPERM) + ')</b>.'
                        }).appendTo(errors);
                        break;
                    }
                    elmnt = new Elmnt(shape, name, fi, height, top, left, color, safe, aisle, between, perRow, rows, chairs, null, showDims);
                }
                default:
                    break;
            }
        }
        if (errors.find('li').length > 0) {
            errors.closest('.alert').css('display', 'block');
            return;
        }
        world.map.space.addElmnt(elmnt);
        $('#objadd form input').val('');
        $('#objadd').modal('hide');
    });
    $('#showAllDims').on('click', () => {
        world.map.space.showAllDims();
    });
    $('#hideAllDims').on('click', () => {
        world.map.space.hideAllDims();
    });
    $(document).on('keydown', (e) => {
        if (e.which == 16) {
            shiftIsPressed = true;
        }
    });
    $(document).keyup((e) => {
        if (shiftIsPressed)
            shiftIsPressed = false;
    });
    $('.obj-templ').draggable({
        scroll: false,
        revert: 'invalid',
        helper: "clone",
        containment: 'document'
    });
    $('.save-project, .close-project-with-save').on('click', function () {
        if (lineMode > 2 && world.lineForEdit != undefined)
            world.map.space.removeLine(world.lineForEdit.id);
        let json = world.prepareToSave();
        $.confirm({
            title: 'Zapis projektu',
            autoClose: 'Zamknij|5000',
            buttons: {
                'Zamknij': function () {
                    return;
                }
            },
            content: function () {
                var self = this;
                let id = json.world.snapshot.projectId;
                let name = json.world.snapshot.projectName;
                let description = json.world.snapshot.projectDesc;
                return $.ajax({
                    url: saveUrl,
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        id: id,
                        name: name,
                        description: description,
                        snapshot: json
                    }
                }).done(function (response) {
                    if (response.error) {
                        self.setContentAppend(response.error);
                        return;
                    }
                    self.setTitle('Sukces');
                    self.setContentAppend('Twój projekt został zapisany.');
                    $('.sidebar-current-project').text('');
                    if (world) {
                        var id = parseInt(response.id);
                        if (id != NaN) {
                            world.setId = id;
                        }
                    }
                }).fail(function () {
                    self.setContentAppend('Coś poszlo nie tak, spróbuj ponownie póżniej lub zgoś usterkę.');
                });
            }
        });
    });
    $('.close-project').on('click', function () {
        if (lineMode > 2 && world.lineForEdit != undefined)
            world.map.space.removeLine(world.lineForEdit.id);
        $.confirm({
            title: 'Zamknięcie projektu',
            content: 'Czy na pewno chcesz zamknąć bez zapisu?',
            autoClose: 'Anuluj|5000',
            buttons: {
                'Zamknij': () => {
                    world.remove();
                    world = null;
                    $('.menuView2').css('display', 'none');
                    $('.menuView9').css('display', 'none');
                    $('.menuView10').css('display', 'none');
                    $('.menuView5').css('display', 'block');
                    $('.sidebar-current-project').text('');
                    loadProjects();
                    $('.ctrls button').removeClass('btn-success').addClass('disabled');
                    $('#toolbar a.nav-link').addClass('disabled');
                },
                'Anuluj': () => {
                    return;
                }
            }
        });
    });
    $('.close-project-with-save').on('click', function () {
        world.remove();
        world = null;
        $('.menuView2').css('display', 'none');
        $('.menuView9').css('display', 'none');
        $('.menuView10').css('display', 'none');
        $('.menuView5').css('display', 'block');
        loadProjects();
        $('.ctrls button').removeClass('btn-success').addClass('disabled');
        $('#toolbar a.nav-link').addClass('disabled');
    });
    $('#finprojj').on('keyup', () => {
        let text = $('#finprojj').val();
        if (text.length < 2) {
            $('.projectlist a').css('display', 'block');
            return;
        }
        $('.projectlist a').each((i, e) => {
            if ($(e).text().toLowerCase().search(text) == -1) {
                $(e).css('display', 'none');
            }
            else {
                $(e).css('display', 'block');
            }
        });
    });
    $('.open-project').on('click', function () {
        var projectList = $('.projectlist .list-group');
        $('.menuView6').css('display', 'block');
        if ($(this).hasClass('show')) {
            $(this).removeClass('show');
            $('.menuView6').css('display', 'none');
            return;
        }
        $(this).addClass('show');
        loadProjects();
    });
    $('.change-photo').on('click', () => {
        $('a.create-project.photo').click();
    });
    $("#menu-toggle").click(function (e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    $('#cropLength').on('change', function () {
        if ($(this).val() < 0)
            $(this).val(0);
    });
    $('#cropWidth').on('change', function () {
        if ($(this).val() < 0)
            $(this).val(0);
    });
    $('#projsavechange').on('click', function () {
        var obj = {
            name: $('#projname').val(),
            description: $('#projdesc').val(),
            id: $('#projid').val()
        };
        $.confirm({
            title: 'Zlecenie aktualizacji',
            content: 'Czy na pewno chcesz zaktualizować projekt?',
            autoClose: 'Anuluj|5000',
            buttons: {
                'Zapisz': function () {
                    $.confirm({
                        title: 'Aktualizacja projektu',
                        buttons: {
                            'Zamknij': function () {
                                return;
                            }
                        },
                        content: function () {
                            var self = this;
                            return $.ajax({
                                url: updateUrl,
                                dataType: 'json',
                                method: 'POST',
                                data: obj
                            }).done(function (response) {
                                if (response.error) {
                                    self.setContentAppend(response.error);
                                    return;
                                }
                                self.close();
                                $('.menuView7').css('display', 'none');
                                $('.menuView5').css('display', 'block');
                                loadProjects();
                            }).fail(function () {
                                self.setContentAppend('Coś poszlo nie tak, spróbuj ponownie póżniej lub zgoś usterkę.');
                            });
                        }
                    });
                },
                'Anuluj': function () {
                    return;
                },
            }
        });
    });
    $('#pomsavechange').on('click', () => {
        let length = $('#pomlength').val();
        let width = $('#pomwidth').val();
        let height = $('#pomheight').val();
        world.map.space.changeLength(length * CMPERM);
        world.map.space.changeWidth(width * CMPERM);
        world.map.space.changeHeight(height * CMPERM);
        world.adjust();
    });
    $('.change-room').on('click', () => {
        let currSize = world.map.space.size;
        $('#pomlength').val(currSize.length / CMPERM);
        $('#pomwidth').val(currSize.width / CMPERM);
        $('#pomheight').val(currSize.height / CMPERM);
        $('.menuView2').css('display', 'none');
        $('.menuView9').css('display', 'none');
        $('.menuView8').css('display', 'block');
    });
    $('#cropHeight').on('change', function () {
        if ($(this).val() < 0)
            $(this).val(0);
    });
    $('#objname').on('keyup', function () {
        world.elmntForEdit.changeName($(this).val());
    });
    $('.editobj input').keypress((e) => {
        if (e.which == 13) {
            $(e.target).focusout();
        }
    });
    $('#objlength').on('focusout', function () {
        world.elmntForEdit.changeLength($(this).val());
    });
    $('#objwidth').on('focusout', function () {
        world.elmntForEdit.changeWidth($(this).val());
    });
    $('#objcolor').on('focusout', function () {
        world.elmntForEdit.changeColor($(this).val());
    });
    $('#objsafe').on('focusout', function () {
        world.elmntForEdit.changeSafe($(this).val());
    });
    $('#objshowdims').on('change', function () {
        world.elmntForEdit.changeShowDims($(this).prop('checked') ? 1 : 0);
    });
    $('.objheight').on('focusout', function () {
        if (parseInt($(this).val()) > world.map.space.size.height) {
            $(this).val(world.map.space.size.height);
        }
        world.elmntForEdit.changeHeight($(this).val());
    });
    $('#objlayup').on('click', function () {
        world.elmntForEdit.changeLayer(1);
    });
    $('#objfi').on('focusout', function () {
        world.elmntForEdit.changeFi($(this).val());
    });
    $('#objlaydown').on('click', function () {
        world.elmntForEdit.changeLayer(-1);
    });
    $('#objcopy').on('click', () => {
        var snapshot = world.elmntForEdit.snapshot;
        snapshot.top = snapshot.left = null;
        world.map.space.addElmnt(null, snapshot);
    });
    $('#objreset').on('click', () => {
        var shape = world.elmntForEdit.shapeText;
        var rstfnc = (el) => {
            $(el).val($(el).data('original'));
            if ($(el).val() != '') {
                $(el).focusout();
            }
        };
        $('.editobj .' + shape + ' input[type="text"]').each((i, el) => rstfnc(el));
        $('.editobj .' + shape + ' input[type="number"]').each((i, el) => rstfnc(el));
        $('.editobj .' + shape + ' input[type="color"]').each((i, el) => rstfnc(el));
        $('.editobj .' + shape + ' input[type="checkbox"]').each((i, el) => {
            $(el).prop('checked', $(el).data('original') == true ? true : false);
            $(el).change();
        });
        $('.editobj .' + shape + ' #fis input').each((i, el) => {
            if ($(el).data('original') == true) {
                let $lbl = $(el).closest('label');
                $(el).data('original', false);
                let val = $lbl.data('value');
                world.elmntForEdit.changeFi(val);
                $('.editobj .' + shape + ' #fis input').prop('checked', false);
                $('.editobj .' + shape + ' #fis label').removeClass('active');
                $(el).prop('checked', true);
                $lbl.addClass('active');
                return;
            }
        });
        $('.editobj .' + shape + ' input[type="radio"].objchrs').each((i, el) => {
            if ($(el).data('original') == true) {
                $(el).data('original', false);
                world.elmntForEdit.changeChairs($(el).data('value'));
                return;
            }
        });
        $('#objname').keyup();
    });
    $('#objremove').on('click', function () {
        $.confirm({
            title: 'Usunięcie elementu',
            content: 'Czy aby na pewno chcesz to zrobić?',
            autoClose: 'Anuluj|5000',
            buttons: {
                'Usuń': function () {
                    world.elmntForEdit.remove();
                },
                'Anuluj': function () {
                    return;
                }
            }
        });
    });
    $('.object-list').on('click', function () {
        var objList = $('.itemlist .list-group');
        if (objList.find('a').length > 1) {
            $('.menuView2').css('display', 'none');
            $('.menuView9').css('display', 'none');
            $('.menuView10').css('display', 'none');
            $('.menuView3').css('display', 'block');
        }
        else {
            var elmnts = [
                {
                    id: 1,
                    name: 'Scena',
                    image: 'fa-microphone',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        length: 100,
                        width: 100,
                        height: 100,
                        color: '#FFFFFF',
                        safe: '10'
                    }
                },
                {
                    id: 2,
                    name: 'Parkiet',
                    image: 'fa-music',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        length: 100,
                        width: 100,
                        height: 100,
                        color: '#FFFFFF',
                        safe: '0'
                    }
                },
                {
                    id: 3,
                    name: 'Ekran',
                    image: 'fa-tv',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        length: 100,
                        width: 100,
                        height: 100,
                        color: '#FFFFFF',
                        safe: '0',
                        forceShowHeight: 1
                    }
                },
                {
                    id: 4,
                    name: 'Zastawka',
                    image: 'fa-coffee',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        length: 100,
                        width: 100,
                        height: 100,
                        color: '#FFFFFF',
                        safe: '0'
                    }
                },
                {
                    id: 5,
                    name: 'Bufety',
                    image: 'fa-cutlery',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        length: 100,
                        width: 100,
                        height: 100,
                        color: '#FFFFFF',
                        safe: '0'
                    }
                },
                {
                    id: 6,
                    name: 'Reżyserka',
                    image: 'fa-video-camera',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        length: 100,
                        width: 100,
                        height: 100,
                        color: '#FFFFFF',
                        safe: '0'
                    }
                },
                {
                    id: 7,
                    name: 'Stoły koktajlowe',
                    image: 'fa-circle-o',
                    shape: ELEMENT_CIRCLES,
                    property: {
                        fi: 60,
                        color: '#FFFFFF',
                        safe: '0',
                        height: 100,
                        rows: 5,
                        perRow: 5,
                        aisle: 50,
                        between: 50,
                    }
                },
                {
                    id: 8,
                    name: 'Krzesła',
                    image: 'fa-th',
                    shape: ELEMENT_CHAIRS,
                    property: {
                        length: 50,
                        width: 50,
                        rows: 5,
                        perRow: 5,
                        aisle: 50,
                        between: 10,
                        color: '#FFFFFF',
                        safe: '0',
                        height: 100
                    }
                },
                {
                    id: 9,
                    name: 'Stoły okrągłe',
                    image: 'fa-asterisk',
                    shape: ELEMENT_TABLES,
                    property: {
                        fi: 160,
                        rows: 2,
                        perRow: 2,
                        aisle: 50,
                        between: 10,
                        color: '#FFFFFF',
                        safe: '30',
                        height: 100,
                        chairs: 8
                    }
                },
                {
                    id: 10,
                    name: 'Własny',
                    image: 'fa-square-o',
                    shape: ELEMENT_RECTANGLE,
                    property: {
                        color: '#FFFFFF'
                    }
                }
            ];
            $.each(elmnts, function (i, e) {
                var $a = jQuery('<a>', {
                    'class': 'obj-templ list-group-item list-group-item-action flex-column align-items-start',
                    'href': "#objtempl" + e.id,
                    'data-object': e.id,
                    'data-name': e.name,
                    'data-shape': e.shape,
                    'data-toggle': 'collapse',
                }).draggable({
                    scroll: false,
                    revert: 'invalid',
                    helper: "clone",
                    containment: 'document'
                })
                    .append(jQuery('<h5>')
                    .append(jQuery('<i>', {
                    class: 'fa ' + e.image
                }), jQuery('<span>', {
                    text: e.name
                })))
                    .appendTo(objList);
                var $properties = $a.find('.card-block');
                $.each(e.property, function (ix, el) {
                    $a.attr('data-' + ix, el);
                    let $div = jQuery('<div>');
                    $div.append(jQuery('<span>', {
                        class: 'property-name',
                        text: translateProperty(ix) + ': '
                    }));
                    var $val = jQuery('<span>');
                    if (ix == 'color') {
                        $val.css({
                            'display': 'inline-block',
                            'background-color': el,
                            'border': '1px solid #a5a5a5',
                            'border-radius': '2px',
                            'width': '20px',
                            'height': '13px',
                            'margin-bottom': '-2px'
                        });
                    }
                    else {
                        $val.text(el);
                    }
                    $val.appendTo($div);
                    let $asd = jQuery('<small>');
                    if (propertyNeedsCm(ix)) {
                        $asd.text(' (cm)');
                    }
                    else if (propertyNeedsSzt(ix)) {
                        $asd.text(' (szt)');
                    }
                    $asd.appendTo($div);
                    $div.prependTo($properties);
                });
            });
            $('.menuView2').css('display', 'none');
            $('.menuView9').css('display', 'none');
            $('.menuView10').css('display', 'none');
            $('.menuView3').css('display', 'block');
        }
    });
    $('.fetch-pdf').on('click', () => {
        $('.map-pdf').click();
    });
    $('.send-mail').on('click', () => {
        $('.map-mail').click();
    });
    $('input.numbr').on('keyup', (e) => {
        $(e.target).val($(e.target).val().replace(/[^0-9]/gi, ''));
    });
    $('.modal').modalTabbing();
    $('.return-to-menuView5').on('click', function () {
        $('.menuView1').css('display', 'none');
        $('.menuView7').css('display', 'none');
        $('.menuView5').css('display', 'block');
        $('.sidebar-current-project').text('');
        $('.ctrls button').removeClass('btn-success').addClass('disabled');
        $('#toolbar a.nav-link').addClass('disabled');
    });
    $('.return-to-menuView2').on('click', function () {
        $('.menuView8').css('display', 'none');
        $('.menuView4').css('display', 'none');
        $('.menuView3').css('display', 'none');
        $('.menuView2').css('display', 'block');
        if (world.map.space.hasImage()) {
            $('.menuView10').css('display', 'block');
        }
        else {
            $('.menuView9').css('display', 'block');
        }
    });
    $('.cancel-objedit').on('click', function () {
        $('.obj').removeClass('editl');
        $('.obj').removeClass('slcted');
    });
    $('#objradius').on('focusout', function () {
        var val = parseInt($(this).val());
        if (val != NaN) {
            world.elmntForEdit.changeRadius(val);
        }
    });
    $('#objrows').on('focusout', function () {
        var val = parseInt($(this).val());
        if (val != NaN) {
            world.elmntForEdit.changeRows(val);
        }
    });
    $('#objperrow').on('focusout', function () {
        var val = parseInt($(this).val());
        if (val != NaN) {
            world.elmntForEdit.changePerRow(val);
        }
    });
    $('#objaisle').on('focusout', function () {
        var val = parseInt($(this).val());
        if (val != NaN) {
            world.elmntForEdit.changeAisle(val);
        }
    });
    $('#objbetween').on('focusout', function () {
        var val = parseInt($(this).val());
        if (val != NaN) {
            world.elmntForEdit.changeBetween(val);
        }
    });
    $('#objrotateleft').on('click', function () {
        var val = parseInt($('#objradius').val());
        if (val != NaN) {
            $('#objradius').val(val - 1);
            world.elmntForEdit.changeRadius(val - 1);
        }
    });
    $('#objrotateright').on('click', function () {
        var val = parseInt($('#objradius').val());
        if (val != NaN) {
            $('#objradius').val(val + 1);
            world.elmntForEdit.changeRadius(val + 1);
        }
    });
    $('a.create-project.new-project').on('click', function () {
        loadProjects();
        $('#projectadd').modal('show');
    });
    $('#projName').keypress((e) => {
        if (e.which == 13) {
            e.preventDefault();
        }
    });
    $('button#projectaddaccept').on('click', function () {
        var name = $('input#projName').val();
        var desc = $('textarea#projDesc').val();
        if (world != null) {
            world.remove();
            world = null;
        }
        world = new World(name, desc);
        $('#projectadd').modal('hide');
        $('.sidebar-current-project').text('')
            .append(jQuery('<h6>', {
            text: 'Projekt: ' + world.name
        }));
        $('.menuView6').css('display', 'none');
        $('.menuView5').css('display', 'none');
        $('.menuView1').css('display', 'block');
    });
    $('.searchProject a').on('click', (e) => {
        let $el = $(e.target);
        $('.searchProject a').removeClass('active');
        $el.addClass('active');
    });
    $('a.create-project.normal').on('click', function () {
        $('#enterDimensions').modal('show');
    });
    $('button#create-normal-accept').on('click', function () {
        $('#enterDimensions').modal('hide');
        var x = $('input#roomLength').val();
        var y = $('input#roomWidth').val();
        var z = $('input#roomHeight').val();
        world.map.createNormalSpace(x * CMPERM, y * CMPERM, z * CMPERM);
        $('#addworldhere').prepend(world.div);
        $('.menuView1').css('display', 'none');
        $('.menuView2').css('display', 'block');
        $('.menuView9').css('display', 'block');
        $('.ctrls button').removeClass('disabled');
        $('#toolbar a.nav-link').removeClass('disabled');
    });
    $('#cropphoto').on('hide.bs.modal', function () {
        cropper.destroy();
    });
    $('a.create-project.photo').on('click', function () {
        if (File && FileReader && FileList && Blob) {
            jQuery('<input>', {
                type: 'file',
                accept: "image/jpeg"
            }).change(function (evt) {
                var files = evt.target.files;
                var file = files[0];
                if (files && file) {
                    var reader = new FileReader();
                    reader.onload = function (readerEvt) {
                        $('#cropphoto img').attr('src', 'data:image/jpeg;base64,' + btoa(readerEvt.target.result));
                        $('#cropphoto img').each(function (i, e) {
                            cropper = new Cropper(this, {
                                movable: false,
                                minContainerWidth: 768,
                                minContainerHeight: 500,
                                autoCrop: false,
                                rotatable: false,
                                scalable: false,
                                zoomable: false
                            });
                        });
                        $('#cropphoto').modal('show');
                    };
                    reader.readAsBinaryString(file);
                }
            }).click();
        }
        else {
            alert('The File APIs are not fully supported in this browser.');
        }
    });
    $('button.map-minus').on('click', function () {
        if ($('button.map-minus').hasClass('disabled'))
            return;
        world.viewScale_minus();
    });
    $('button.map-mail').on('click', function () {
        if ($('button.map-plus').hasClass('disabled'))
            return;
        $('#topico').val('Projekt pomieszczenia: ' + world.name);
        $('#sendemail').modal('show');
    });
    $('button.map-plus').on('click', function () {
        if ($('button.map-plus').hasClass('disabled'))
            return;
        world.viewScale_plus();
    });
    $('button.map-up').on('click', function () {
        if ($('button.map-up').hasClass('disabled'))
            return;
        world.map.moveup();
    });
    $('button.map-down').on('click', function () {
        if ($('button.map-down').hasClass('disabled'))
            return;
        world.map.movedown();
    });
    $('button.map-left').on('click', function () {
        if ($('button.map-left').hasClass('disabled'))
            return;
        world.map.moveleft();
    });
    $('button.map-right').on('click', function () {
        if ($('button.map-right').hasClass('disabled'))
            return;
        world.map.moveright();
    });
    $('button.map-adjust').on('click', function () {
        if ($('button.map-adjust').hasClass('disabled'))
            return;
        world.adjust();
    });
    $('.ctrls button').on('click', (e) => {
        if (world === null) {
            e.preventDefault();
            return false;
        }
    });
    $('.map-block').on('click', function () {
        if ($(this).hasClass('disabled'))
            return;
        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success');
            world.map.div.draggable("option", "cancel", null);
        }
        else {
            $(this).addClass('btn-success');
            world.map.div.draggable("option", "cancel", '.room');
        }
    });
    $('.searchProject a').on('click', () => sortProjects());
    $('[data-toggle="tooltip"]').tooltip();
    $('.map-line').on('click', function () {
        if ($(this).hasClass('disabled'))
            return;
        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success');
            if (lineMode == 2) {
                world.map.space.removeLine(world.lineForEdit);
            }
            lineMode = 0;
            $('.map-measure').removeClass('disabled');
        }
        else {
            $(this).addClass('btn-success');
            lineMode = 1;
            $('.map-measure').addClass('disabled');
        }
    });
    $('.map-measure').on('click', function () {
        if ($(this).hasClass('disabled'))
            return;
        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success');
            if (world.lineForEdit != undefined)
                world.map.space.removeLine(world.lineForEdit.id);
            lineMode = 0;
            $('.map-line').removeClass('disabled');
        }
        else {
            $(this).addClass('btn-success');
            lineMode = 3;
            $('.map-line').addClass('disabled');
        }
    });
    $(window).on('keydown', function (e) {
        if (e.keyCode == 37)
            world.map.moveleft();
        if (e.keyCode == 38)
            world.map.moveup();
        if (e.keyCode == 39)
            world.map.moveright();
        if (e.keyCode == 40)
            world.map.movedown();
    });
});
function loadProjects() {
    $('.projectlist .list-group').text('');
    $.confirm({
        title: 'Pobieranie projektów',
        autoClose: 'Zamknij|5000',
        buttons: {
            'Zamknij': function () {
                return;
            }
        },
        content: function () {
            var self = this;
            return $.ajax({
                url: indexUrl,
                dataType: 'json',
                method: 'GET'
            }).done(function (response) {
                if (response.error) {
                    self.setContentAppend(response.error);
                    return;
                }
                self.close();
                processProjects(response);
            }).fail(function () {
                self.setContentAppend('Coś poszlo nie tak, spróbuj ponownie póżniej lub zgoś usterkę.');
            });
        }
    });
}
function processProjects(projects) {
    var projectList = $('.projectlist .list-group');
    $.each(projects, function (i, e) {
        jQuery('<a>', {
            'class': 'list-group-item list-group-item-action flex-column align-items-start',
            'href': "#projectdetails" + e.id,
            'data-project': e.id,
            'data-project-name': e.name,
            'data-project-desc': e.description,
            'data-project-creationdate': e.creationDate,
            'data-project-lastusedate': e.lastUseDate,
            'data-toggle': 'collapse'
        })
            .append(jQuery('<h5>')
            .append(jQuery('<i>', {
            class: 'fa fa-folder'
        }), jQuery('<span>', {
            text: e.name
        })), jQuery('<div>', {
            class: 'collapse',
            id: 'projectdetails' + e.id
        }).on('show.bs.collapse', function (e) {
            $('.collapse').collapse("hide");
        })
            .append(jQuery('<div>', {
            class: 'card-block'
        })
            .append(jQuery('<div>')
            .append(jQuery('<span>', {
            class: 'property-name',
            text: 'Utworzenie: '
        }), jQuery('<span>', {
            text: new Date(e.creationDate * 1000).toLocaleString()
        })), jQuery('<div>')
            .append(jQuery('<span>', {
            class: 'property-name',
            text: 'Ostatnia edycja: '
        }), jQuery('<span>', {
            text: new Date(e.lastUseDate * 1000).toLocaleString()
        })), jQuery('<div>')
            .append(jQuery('<span>', {
            class: 'property-name',
            text: 'Opis: '
        }), jQuery('<span>', {
            text: e.description
        })), jQuery('<div>')
            .append(jQuery('<button>', {
            class: 'btn btn-sm btn-danger',
            type: 'button',
            text: 'Usuń'
        }).on('click', function () {
            var id = $(this).closest('a').data('project');
            $.confirm({
                title: 'Zlecenie usunięcia',
                content: 'Czy na pewno chcesz usunąć projekt?',
                autoClose: 'Anuluj|5000',
                buttons: {
                    'Usuń': function () {
                        $.confirm({
                            title: 'Usuwanie projektu',
                            buttons: {
                                'Zamknij': function () {
                                    return;
                                }
                            },
                            content: function () {
                                var self = this;
                                return $.ajax({
                                    url: deleteUrl,
                                    dataType: 'json',
                                    method: 'POST',
                                    data: { id: id }
                                }).done(function (response) {
                                    if (response.error) {
                                        self.setContentAppend(response.error);
                                        return;
                                    }
                                    self.close();
                                    $('a[data-project="' + id + '"]').remove();
                                }).fail(function () {
                                    self.setContentAppend('Coś poszlo nie tak, spróbuj ponownie póżniej lub zgoś usterkę.');
                                });
                            }
                        });
                    },
                    'Anuluj': function () {
                        return;
                    },
                }
            });
        }), jQuery('<button>', {
            class: 'btn btn-sm btn-secondary',
            type: 'button',
            text: 'Edytuj'
        }).on('click', function () {
            $('.menuView5').css('display', 'none');
            $('.menuView6').css('display', 'none');
            $('.menuView7').css('display', 'block');
            var name = $(this).closest('a').data('project-name');
            var desc = $(this).closest('a').data('project-desc');
            var id = $(this).closest('a').data('project');
            $('.projname').text(name);
            $('#projname').val(name);
            $('#projdesc').val(desc);
            $('#projid').val(id);
        }), jQuery('<button>', {
            class: 'btn btn-sm btn-secondary',
            type: 'button',
            text: 'Otwórz'
        }).on('click', function () {
            var id = $(this).closest('a').data('project');
            $.confirm({
                title: 'Otwieranie projektu',
                buttons: {
                    'Zamknij': function () {
                        return;
                    }
                },
                content: function () {
                    var self = this;
                    return $.ajax({
                        url: loadUrl,
                        dataType: 'json',
                        method: 'POST',
                        data: { id: id }
                    }).done(function (response) {
                        if (response.error) {
                            self.setContentAppend(response.error);
                            return;
                        }
                        self.close();
                        var project = response;
                        if (world != null) {
                            world.remove();
                            world = null;
                        }
                        world = new World(project.name, project.description, parseInt(project.id), project.snapshot.world.map);
                        $('#addworldhere').prepend(world.div);
                        $('.sidebar-current-project').text('')
                            .append(jQuery('<h6>', {
                            text: 'Projekt: ' + project.name
                        }));
                        $('.menuView5').css('display', 'none');
                        $('.menuView6').css('display', 'none');
                        $('.menuView2').css('display', 'block');
                        $('.ctrls button').removeClass('disabled');
                        $('#toolbar a.nav-link').removeClass('disabled');
                        if (project.snapshot.world.map.space.image) {
                            $('.menuView10').css('display', 'block');
                        }
                        else {
                            $('.menuView9').css('display', 'block');
                        }
                    }).fail(function () {
                        self.setContentAppend('Coś poszlo nie tak, spróbuj ponownie póżniej lub zgoś usterkę.');
                    });
                }
            });
        }))))
            .collapse('hide'))
            .appendTo(projectList);
    });
    $(".modal").on("hidden.bs.modal", function () {
        $(this).find('form')[0].reset();
    });
    $('.objchrs').on('click', (e) => {
        let nms = $(e.target).data('value');
        world.elmntForEdit.changeChairs(nms);
    });
}
var translateProperty = (property) => {
    let words = {
        chairs: 'Ilość krzeseł',
        height: 'Wysokość',
        safe: 'Bezpieczna odległość',
        color: 'Kolor',
        between: 'Pomiędzy w x',
        aisle: 'Pomiędzy w y',
        perRow: 'Ilość w rzędzie',
        rows: 'Ilość rzędów',
        fi: 'Średnica',
        width: 'Szerokość',
        length: 'Długość'
    };
    return words[property];
};
var propertyNeedsCm = (property) => {
    let words = ['height', 'safe', 'between', 'aisle', 'fi', 'width', 'length'];
    return words.find((word) => word == property) != undefined;
};
var propertyNeedsSzt = (property) => {
    let words = ['chairs', 'perRow', 'rows'];
    return words.find((word) => word == property) != undefined;
};
var blabla = (element) => {
    let val = $(element).data('value');
    $('.objchrs').prop('checked', false);
    $('.objchrs[data-value="10"]').prop('checked', true);
    if (val == 160) {
        $('.objchrs[data-value="8"]').prop('disabled', false);
        $('.objchrs[data-value="12"]').prop('disabled', true);
    }
    else {
        $('.objchrs[data-value="8"]').prop('disabled', true);
        $('.objchrs[data-value="12"]').prop('disabled', false);
    }
    if ($(element).closest('.editobj').length > 0) {
        world.elmntForEdit.changeFi(val);
    }
};
var sortProjects = () => {
    let type = $('.searchProject a.active').data('key');
    let $list = $('.projectlist .list-group');
    var $sorted = $list.children().detach().sort((a, b) => {
        switch (type) {
            case 1: return $(a).attr('data-project-lastusedate').toString().localeCompare($(b).attr('data-project-lastusedate'));
            case 2:
            case 3:
                return $(a).attr('data-project-creationdate').toString().localeCompare($(b).attr('data-project-creationdate'));
            case 4:
            case 5:
                return $(a).find('h5 > span').text().toLowerCase().localeCompare($(b).find('h5 > span').text().toLowerCase());
            default: return false;
        }
    });
    let arr = jQuery.makeArray($sorted);
    switch (type) {
        case 2:
        case 5:
            arr.reverse();
            break;
    }
    $(arr).appendTo($list);
};
var cm2m = (cms) => {
    let m = Math.floor(cms / CMPERM);
    let cm = Math.floor(cms % CMPERM);
    let str = '';
    if (m > 0)
        str += m + 'm ';
    if (cm > 0)
        str += cm + 'cm';
    return str;
};
class Mapz {
    get div() {
        return this.holder;
    }
    get space() {
        return this.spaceInstance;
    }
    get snapshot() {
        var obj = {
            width: this.width,
            height: this.height
        };
        return obj;
    }
    constructor(width, height, space = null) {
        this.width = width || 10000;
        this.height = height || 10000;
        this.create(space);
    }
    create(space = null) {
        this.holder = jQuery('<div>', {
            class: 'map'
        }).draggable({
            scroll: false
        }).css({
            'width': this.width,
            'height': this.height
        });
        this.centerPosition();
        if (space) {
            if (space.image) {
                this.createPhotoSpace(space.image, space.length, space.width, space.height, space.elmnts, space.lines);
            }
            else {
                this.createNormalSpace(space.length, space.width, space.height, space.elmnts, space.lines);
            }
        }
    }
    createNormalSpace(length, width, height, elmnts = null, lines = null) {
        this.clean();
        this.spaceInstance = new NormalSpace(length, width, height, elmnts, lines);
        this.holder.append(this.spaceInstance.room.div);
    }
    createPhotoSpace(image, length, width, height, elmnts = null, lines = null) {
        this.clean();
        this.spaceInstance = new PhotoSpace(image, length, width, height, elmnts, lines);
        this.holder.append(this.spaceInstance.room.div);
    }
    clean() {
        this.holder.html('');
        this.spaceInstance = null;
    }
    centerPosition() {
        this.holder.css({
            'left': 'calc(50% - ' + (this.width / 2) + 'px)',
            'top': 'calc(50% - ' + (this.height / 2) + 'px)'
        });
    }
    moveup() {
        var off = this.holder.offset();
        this.holder.offset({ top: off.top + MAPMOVESTEP, left: off.left });
    }
    movedown() {
        var off = this.holder.offset();
        this.holder.offset({ top: off.top - MAPMOVESTEP, left: off.left });
    }
    moveleft() {
        var off = this.holder.offset();
        this.holder.offset({ top: off.top, left: off.left + MAPMOVESTEP });
    }
    moveright() {
        var off = this.holder.offset();
        this.holder.offset({ top: off.top, left: off.left - MAPMOVESTEP });
    }
    zoom(value) {
        this.spaceInstance.zoom(value);
        this.spaceInstance.room.zoomElmnts(value);
    }
}
class NormalSpace {
    get div() {
        return this.holder;
    }
    get room() {
        return this.roomInstance;
    }
    hasImage() {
        return false;
    }
    get size() {
        return {
            length: parseInt(this.length.toString()),
            width: parseInt(this.width.toString()),
            height: parseInt(this.height.toString())
        };
    }
    get snapshot() {
        var obj = {
            length: this.length,
            width: this.width,
            height: this.height,
            elmnts: [],
            lines: []
        };
        $.each(this.elmnts, function (i, e) {
            obj.elmnts.push(e.snapshot);
        });
        $.each(this.lines, function (i, e) {
            obj.lines.push(e.snapshot);
        });
        return obj;
    }
    constructor(length, width, height, elmnts = null, lines = null) {
        this.elmnts = [];
        this.lines = [];
        this.length = length || 200;
        this.width = width || 200;
        this.height = height || 10;
        this.create(elmnts, lines);
    }
    showAllDims() {
        $.each(this.elmnts, (i, e) => {
            e.dims = 1;
        });
    }
    hideAllDims() {
        $.each(this.elmnts, (i, e) => {
            e.dims = 0;
        });
    }
    changeLength(length) {
        if (length)
            this.length = length;
        this.holder.css({
            'width': world.scaleValue(this.length)
        });
        this.room.dimensions(this.length);
    }
    changeWidth(width) {
        if (width)
            this.width = width;
        this.holder.css({
            'height': world.scaleValue(this.width)
        });
        this.room.dimensions(null, this.width);
    }
    changeHeight(height) {
        if (height)
            this.height = height;
    }
    addLine(x1, y1, x2 = null, y2 = null, width = null, height = null) {
        var line = new Line(x1, y1, x2, y2, width, height);
        this.lines.push(line);
        return line;
    }
    scale(calc) {
        this.holder.css({
            'width': calc(this.length),
            'height': calc(this.width)
        });
        this.roomInstance.div.find('> .box_height').css('width', calc(this.width) + 20);
        $.each(this.elmnts, function (i, e) {
            e.scale(calc);
        });
        $.each(this.lines, function (i, e) {
            e.scale(calc);
        });
    }
    addElmnt(elmnt, e = null) {
        if (elmnt == null) {
            var shape = parseInt(e.shape);
            switch (shape) {
                case ELEMENT_RECTANGLE: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.length), parseInt(e.width), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims), parseInt(e.forceShowHeight));
                    break;
                }
                case ELEMENT_CIRCLES: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.fi), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.aisle), parseInt(e.between), parseInt(e.perRow), parseInt(e.rows), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims));
                    break;
                }
                case ELEMENT_CHAIRS: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.length), parseInt(e.width), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.aisle), parseInt(e.between), parseInt(e.perRow), parseInt(e.rows), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims));
                    break;
                }
                case ELEMENT_TABLES: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.fi), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.aisle), parseInt(e.between), parseInt(e.perRow), parseInt(e.rows), parseInt(e.chairs), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims));
                    break;
                }
            }
        }
        this.elmnts.push(elmnt);
        return elmnt.variant;
    }
    removeElmnt(id) {
        var obj = this.elmnts.find(x => x.id === id);
        if (obj) {
            obj.remove();
            this.elmnts.splice(this.elmnts.indexOf(obj), 1);
        }
    }
    removeLine(id) {
        var line = this.lines.find(x => x.id === id);
        if (line) {
            line.remove();
            this.lines.splice(this.lines.indexOf(line), 1);
        }
    }
    create(elmnts = null, lines = null) {
        this.roomInstance = new Room();
        var parent = this;
        this.holder = jQuery('<div>', {
            class: 'space'
        }).css({
            'width': this.length,
            'height': this.width
        }).on('click', function (e) {
            if (lineMode) {
                var parentOffset = parent.holder.offset();
                var x = e.pageX - parentOffset.left;
                var y = e.pageY - parentOffset.top;
                world.addLineCord(x, y);
            }
        }).on('mousemove', (e) => {
            if (lineMode == 2 || lineMode == 4) {
                var parentOffset = parent.holder.offset();
                var x = e.pageX - parentOffset.left;
                var y = e.pageY - parentOffset.top;
                world.lineForEdit.mouse(x, y);
            }
        }).droppable({
            drop: function (event, ui) {
                if (ui.draggable.hasClass('obj-templ')) {
                    var paPos = parent.holder.offset();
                    $('#objadd .alert').css('display', 'none');
                    $('#objadd input#objName').val(ui.draggable.data('name'));
                    $('#objadd input#objShape').val(ui.draggable.data('shape'));
                    $('#objadd input#objColor').val(ui.draggable.data('color'));
                    $('#objadd input#objTop').val(world.realViewScale(event.clientY - paPos.top));
                    $('#objadd input#objLeft').val(world.realViewScale(event.clientX - paPos.left));
                    $('#objadd input#objSafe').val(ui.draggable.data('safe'));
                    switch (ui.draggable.data('shape')) {
                        case ELEMENT_RECTANGLE: {
                            $('#objadd form .col').css('display', 'none');
                            $('#objadd form .col.rect').css('display', 'block');
                            $('#objadd .rect input#objLength').val(ui.draggable.data('length'));
                            $('#objadd .rect input#objWidth').val(ui.draggable.data('width'));
                            $('#objadd .rect input#objHeight').val(ui.draggable.data('height'));
                            $('#objadd input#objForceShowHeight').val(ui.draggable.data('forceshowheight'));
                            $('#objadd').modal('show');
                            break;
                        }
                        case ELEMENT_CIRCLES: {
                            $('#objadd form .col').css('display', 'none');
                            $('#objadd form .col.circles').css('display', 'block');
                            $('#objadd .circles input#objFi').val(ui.draggable.data('fi'));
                            $('#objadd .circles input#objHeight').val(ui.draggable.data('height'));
                            $('#objadd .circles input#objAisle').val(ui.draggable.data('aisle'));
                            $('#objadd .circles input#objPerRow').val(ui.draggable.data('perrow'));
                            $('#objadd .circles input#objRows').val(ui.draggable.data('rows'));
                            $('#objadd .circles input#objBetween').val(ui.draggable.data('between'));
                            $('#objadd').modal('show');
                            break;
                        }
                        case ELEMENT_CHAIRS: {
                            $('#objadd form .col').css('display', 'none');
                            $('#objadd form .col.chairs').css('display', 'block');
                            $('#objadd .chairs input#objLength').val(ui.draggable.data('length'));
                            $('#objadd .chairs input#objWidth').val(ui.draggable.data('width'));
                            $('#objadd .chairs input#objHeight').val(ui.draggable.data('height'));
                            $('#objadd .chairs input#objAisle').val(ui.draggable.data('aisle'));
                            $('#objadd .chairs input#objPerRow').val(ui.draggable.data('perrow'));
                            $('#objadd .chairs input#objRows').val(ui.draggable.data('rows'));
                            $('#objadd .chairs input#objBetween').val(ui.draggable.data('between'));
                            $('#objadd').modal('show');
                            break;
                        }
                        case ELEMENT_TABLES: {
                            $('#objadd form .col').css('display', 'none');
                            $('#objadd form .col.tables').css('display', 'block');
                            let fi = ui.draggable.data('fi');
                            $('#objadd .tables #fis label').removeClass('active');
                            $('#objadd .tables #fis label[data-value="' + fi + '"]').addClass('active');
                            $('#objadd .tables #fis label[data-value="' + fi + '"] input').prop('checked', true);
                            let chrs = ui.draggable.data('chairs');
                            $('.objchrs').prop('checked', false);
                            $('.objchrs[data-value="' + chrs + '"]').prop('checked', true);
                            $('.objchrs').prop('disabled', false);
                            $('.objchrs[data-value="' + (fi == 160 ? 12 : 8) + '"]').prop('disabled', true);
                            $('#objadd .tables input#objHeight').val(ui.draggable.data('height'));
                            $('#objadd .tables input#objAisle').val(ui.draggable.data('aisle'));
                            $('#objadd .tables input#objPerRow').val(ui.draggable.data('perrow'));
                            $('#objadd .tables input#objRows').val(ui.draggable.data('rows'));
                            $('#objadd .tables input#objBetween').val(ui.draggable.data('between'));
                            $('#objadd').modal('show');
                        }
                        default:
                            break;
                    }
                }
            }
        }).on('mousemove', (e) => {
            if (isMouseDown) {
                var $cl = this.holder.find('.clonie');
                if ($cl.length) {
                    var tp = e.pageY - ($cl.height() / 2);
                    var lt = e.pageX - ($cl.width() / 2);
                    $cl.offset({ top: tp, left: lt });
                }
            }
        }).on('mouseup', () => {
            var $cl = this.holder.find('.clonie');
            if ($cl.length) {
                var snap = $cl.data('snapshot');
                var newElmnt = world.map.space.addElmnt(null, snap);
                var myPos = $cl.offset();
                var paPos = $cl.parent().offset();
                var top = world.realViewScale(myPos.top - paPos.top);
                var left = world.realViewScale(myPos.left - paPos.left);
                newElmnt.changePosition(top, left);
                $cl.remove();
            }
        }).appendTo(this.roomInstance.div);
        this.roomInstance.dimensions(this.length, this.width);
        $.onCreate('div.space', function () {
            if (elmnts) {
                $.each(elmnts, function (i, e) {
                    parent.addElmnt(null, e);
                });
            }
            if (lines) {
                $.each(lines, function (i, e) {
                    parent.addLine(e.x1, e.y1, e.x2, e.y2, e.width, e.height);
                });
            }
            world.adjust();
        });
    }
}
class PhotoSpace {
    get div() {
        return this.holder;
    }
    get room() {
        return this.roomInstance;
    }
    get size() {
        return {
            length: this.length,
            width: this.width,
            height: this.height
        };
    }
    changeLength(length) {
        if (length)
            this.length = length;
        this.holder.css({
            'width': world.scaleValue(this.length)
        });
        this.room.dimensions(this.length);
    }
    changeWidth(width) {
        if (width)
            this.width = width;
        this.holder.css({
            'height': world.scaleValue(this.width)
        });
        this.room.dimensions(null, this.width);
    }
    hasImage() {
        return this.image != null;
    }
    showAllDims() {
        $.each(this.elmnts, (i, e) => {
            e.dims = 1;
        });
    }
    hideAllDims() {
        $.each(this.elmnts, (i, e) => {
            e.dims = 0;
        });
    }
    changePhoto(photo) {
        if (photo)
            this.image = photo;
        this.holder.css({
            'background-image': 'url(' + this.image + ')',
            'background-size': world.scaleValue(this.length) + "px " + world.scaleValue(this.width) + "px",
        });
    }
    changeHeight(height) {
        if (height)
            this.height = height;
    }
    get snapshot() {
        var obj = {
            length: this.length,
            width: this.width,
            height: this.height,
            image: this.image,
            elmnts: [],
            lines: []
        };
        $.each(this.elmnts, function (i, e) {
            obj.elmnts.push(e.snapshot);
        });
        $.each(this.lines, function (i, e) {
            obj.lines.push(e.snapshot);
        });
        return obj;
    }
    constructor(image, length, width, height, elmnts = null, lines = null) {
        this.elmnts = [];
        this.lines = [];
        this.image = image;
        this.length = length;
        this.width = width;
        this.height = height;
        this.create(elmnts, lines);
    }
    addLine(x1, y1, x2 = null, y2 = null, width = null, height = null) {
        var line = new Line(x1, y1, x2, y2, width, height);
        this.lines.push(line);
        return line;
    }
    scale(calc) {
        this.holder.css({
            'width': calc(this.length),
            'height': calc(this.width),
            'background-size': calc(this.length) + "px " + calc(this.width) + "px",
        });
        this.roomInstance.div.find('> .box_height').css('width', calc(this.width) + 20);
        $.each(this.elmnts, function (i, e) {
            e.scale(calc);
        });
        $.each(this.lines, function (i, e) {
            e.scale(calc);
        });
    }
    addElmnt(elmnt, e = null) {
        if (elmnt == null) {
            var shape = parseInt(e.shape);
            switch (shape) {
                case ELEMENT_RECTANGLE: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.length), parseInt(e.width), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims), parseInt(e.forceShowHeight));
                    break;
                }
                case ELEMENT_CIRCLES: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.fi), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.layer), parseInt(e.showDims));
                    break;
                }
                case ELEMENT_CHAIRS: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.length), parseInt(e.width), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.aisle), parseInt(e.between), parseInt(e.perRow), parseInt(e.rows), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims));
                    break;
                }
                case ELEMENT_TABLES: {
                    elmnt = new Elmnt(shape, e.name, parseInt(e.fi), parseInt(e.height), parseFloat(e.top), parseFloat(e.left), e.color, parseInt(e.safe), parseInt(e.aisle), parseInt(e.between), parseInt(e.perRow), parseInt(e.rows), parseInt(e.chairs), parseInt(e.radius), parseInt(e.layer), parseInt(e.showDims));
                    break;
                }
            }
        }
        this.elmnts.push(elmnt);
    }
    removeElmnt(id) {
        var obj = this.elmnts.find(x => x.id === id);
        if (obj) {
            obj.remove();
            this.elmnts.splice(this.elmnts.indexOf(obj), 1);
        }
    }
    removeLine(id) {
        var line = this.lines.find(x => x.id === id);
        if (line) {
            line.remove();
            this.lines.splice(this.lines.indexOf(line), 1);
        }
    }
    create(elmnts = null, lines = null) {
        this.roomInstance = new Room();
        var img = new Image();
        img.onload = () => {
            var parent = this;
            this.holder = jQuery('<div>', {
                class: 'space'
            }).css({
                'background-image': 'url(' + this.image + ')',
                'background-size': this.length + "px " + this.width + "px",
                'width': this.length,
                'height': this.width
            }).on('click', function (e) {
                if (lineMode) {
                    var parentOffset = parent.holder.offset();
                    var x = e.pageX - parentOffset.left;
                    var y = e.pageY - parentOffset.top;
                    world.addLineCord(x, y);
                }
            }).on('mousemove', (e) => {
                if (lineMode == 2 || lineMode == 4) {
                    var parentOffset = parent.holder.offset();
                    var x = e.pageX - parentOffset.left;
                    var y = e.pageY - parentOffset.top;
                    world.lineForEdit.mouse(x, y);
                }
            }).droppable({
                drop: function (event, ui) {
                    if (ui.draggable.hasClass('obj-templ')) {
                        var paPos = parent.holder.offset();
                        $('#objadd .alert').css('display', 'none');
                        $('#objadd input#objName').val(ui.draggable.data('name'));
                        $('#objadd input#objShape').val(ui.draggable.data('shape'));
                        $('#objadd input#objColor').val(ui.draggable.data('color'));
                        $('#objadd input#objTop').val(world.realViewScale(event.clientY - paPos.top));
                        $('#objadd input#objLeft').val(world.realViewScale(event.clientX - paPos.left));
                        $('#objadd input#objSafe').val(ui.draggable.data('safe'));
                        switch (ui.draggable.data('shape')) {
                            case ELEMENT_RECTANGLE: {
                                $('#objadd form .col').css('display', 'none');
                                $('#objadd form .col.rect').css('display', 'block');
                                $('#objadd .rect input#objLength').val(ui.draggable.data('length'));
                                $('#objadd .rect input#objWidth').val(ui.draggable.data('width'));
                                $('#objadd .rect input#objHeight').val(ui.draggable.data('height'));
                                $('#objadd input#objForceShowHeight').val(ui.draggable.data('forceshowheight'));
                                $('#objadd').modal('show');
                                break;
                            }
                            case ELEMENT_CIRCLES: {
                                $('#objadd form .col').css('display', 'none');
                                $('#objadd form .col.circle').css('display', 'block');
                                $('#objadd .circle input#objFi').val(ui.draggable.data('fi'));
                                $('#objadd .circle input#objHeight').val(ui.draggable.data('height'));
                                $('#objadd').modal('show');
                                break;
                            }
                            case ELEMENT_CHAIRS: {
                                $('#objadd form .col').css('display', 'none');
                                $('#objadd form .col.chairs').css('display', 'block');
                                $('#objadd .chairs input#objLength').val(ui.draggable.data('length'));
                                $('#objadd .chairs input#objWidth').val(ui.draggable.data('width'));
                                $('#objadd .chairs input#objHeight').val(ui.draggable.data('height'));
                                $('#objadd .chairs input#objAisle').val(ui.draggable.data('aisle'));
                                $('#objadd .chairs input#objPerRow').val(ui.draggable.data('perrow'));
                                $('#objadd .chairs input#objRows').val(ui.draggable.data('rows'));
                                $('#objadd .chairs input#objBetween').val(ui.draggable.data('between'));
                                $('#objadd').modal('show');
                                break;
                            }
                            case ELEMENT_TABLES: {
                                $('#objadd form .col').css('display', 'none');
                                $('#objadd form .col.tables').css('display', 'block');
                                let fi = ui.draggable.data('fi');
                                $('#objadd .tables #fis label').removeClass('active');
                                $('#objadd .tables #fis label[data-value="' + fi + '"]').addClass('active');
                                $('#objadd .tables #fis label[data-value="' + fi + '"] input').prop('checked', true);
                                let chrs = ui.draggable.data('chairs');
                                $('.objchrs').prop('checked', false);
                                $('.objchrs[data-value="' + chrs + '"]').prop('checked', true);
                                $('.objchrs').prop('disabled', false);
                                $('.objchrs[data-value="' + (fi == 160 ? 12 : 8) + '"]').prop('disabled', true);
                                $('#objadd .tables input#objHeight').val(ui.draggable.data('height'));
                                $('#objadd .tables input#objAisle').val(ui.draggable.data('aisle'));
                                $('#objadd .tables input#objPerRow').val(ui.draggable.data('perrow'));
                                $('#objadd .tables input#objRows').val(ui.draggable.data('rows'));
                                $('#objadd .tables input#objBetween').val(ui.draggable.data('between'));
                                $('#objadd').modal('show');
                            }
                            default:
                                break;
                        }
                    }
                }
            }).on('mousemove', (e) => {
                if (isMouseDown) {
                    var $cl = this.holder.find('.clonie');
                    if ($cl.length) {
                        var tp = e.pageY - ($cl.height() / 2);
                        var lt = e.pageX - ($cl.width() / 2);
                        $cl.offset({ top: tp, left: lt });
                    }
                }
            }).on('mouseup', () => {
                var $cl = this.holder.find('.clonie');
                if ($cl.length) {
                    var snap = $cl.data('snapshot');
                    var newElmnt = world.map.space.addElmnt(null, snap);
                    var myPos = $cl.offset();
                    var paPos = $cl.parent().offset();
                    var top = world.realViewScale(myPos.top - paPos.top);
                    var left = world.realViewScale(myPos.left - paPos.left);
                    newElmnt.changePosition(top, left);
                    $cl.remove();
                }
            }).appendTo(this.roomInstance.div);
            this.roomInstance.dimensions(this.length, this.width);
            $.onCreate('div.space', function () {
                if (elmnts) {
                    $.each(elmnts, function (i, e) {
                        parent.addElmnt(null, e);
                    });
                }
                if (lines) {
                    $.each(lines, function (i, e) {
                        parent.addLine(e.x1, e.y1, e.x2, e.y2, e.width, e.height);
                    });
                }
                world.adjust();
            });
        };
        img.src = this.image;
    }
}
class Rectangle {
    get shapeText() {
        return 'rect';
    }
    get position() {
        return {
            top: this.top,
            left: this.left
        };
    }
    get div() {
        return this.holder;
    }
    get size() {
        return {
            width: this.width,
            length: this.length,
            height: this.height
        };
    }
    get snapshot() {
        var obj = {
            shape: ELEMENT_RECTANGLE,
            name: this.name,
            length: this.length,
            width: this.width,
            height: this.height,
            radius: this.radius,
            color: this.color,
            top: this.top,
            left: this.left,
            layer: this.layer,
            safe: this.safe,
            showDims: this.showDims,
            forceShowHeight: this.forceShowHeight
        };
        return obj;
    }
    get getUniqueId() {
        return this.uniqueId;
    }
    constructor(...properties) {
        this.uniqueId = Math.floor(Math.random() * 1000000);
        this.name = properties[0] || '';
        this.length = properties[1] || 100;
        this.width = properties[2] || 100;
        this.height = properties[3] || 100;
        this.top = properties[4] || 0;
        this.left = properties[5] || 0;
        this.color = properties[6] || '#ffffff';
        this.safe = properties[7];
        this.radius = properties[8] || 0;
        this.layer = properties[9] || 10;
        this.showDims = properties[10] || 0;
        this.forceShowHeight = properties[11] || 0;
        this.create();
    }
    scale(calc) {
        var scl = ((this.length / 7) + (this.width / 7)) / 2;
        this.holder.find('.title').css('font-size', calc(scl));
        this.holder.find('.box_height').css({
            'width': calc(this.width)
        });
        let smlr = (this.length + this.width) / 2;
        let fntsize = smlr / 12;
        if (calc(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (calc(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.css({
            'width': calc(this.calcSafe(this.length)),
            'height': calc(this.calcSafe(this.width)),
            'top': calc(this.top),
            'left': calc(this.left),
            'padding': calc(this.safe)
        });
    }
    resize() {
        var scl = ((this.length / 7) + (this.width / 7)) / 2;
        this.holder.find('.title').css('font-size', world.scaleValue(scl));
        this.holder.find('.box_height').css('width', world.scaleValue(this.width));
        let smlr = (this.length + this.width) / 2;
        let fntsize = smlr / 12;
        if (world.scaleValue(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (world.scaleValue(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
    }
    changeShowDims(show) {
        this.showDims = parseInt(show);
        if (this.showDims == 0) {
            this.holder.find('.box_width').addClass('hdn');
            this.holder.find('.box_height').addClass('hdn');
        }
        else {
            this.holder.find('.box_width').removeClass('hdn');
            this.holder.find('.box_height').removeClass('hdn');
        }
        this.resize();
    }
    changePosition(top, left) {
        this.top = parseInt(top);
        this.left = parseInt(left);
        this.holder.css('top', world.scaleValue(this.calcSafe(this.top)));
        this.holder.css('left', world.scaleValue(this.calcSafe(this.left)));
    }
    changeName(name) {
        this.name = name;
        this.holder.find('.title span').text(name);
    }
    changeLength(length) {
        this.length = parseInt(length);
        this.holder.css('width', world.scaleValue(this.calcSafe(this.length)));
        if (this.forceShowHeight) {
            this.holder.find('.box_height span').text(cm2m(this.length) + ' x ' + cm2m(this.width));
        }
        else {
            this.holder.find('.box_width span').text(cm2m(this.length));
        }
        this.resize();
    }
    changeSafe(safe) {
        this.safe = parseInt(safe);
        this.holder.css({
            'padding': world.scaleValue(safe),
            'width': world.scaleValue(this.calcSafe(this.length)),
            'height': world.scaleValue(this.calcSafe(this.width)),
        });
        this.resize();
    }
    changeWidth(width) {
        this.width = parseInt(width);
        this.holder.css('height', world.scaleValue(this.calcSafe(this.width)));
        if (this.forceShowHeight) {
            this.holder.find('.box_height span').text(cm2m(this.length) + ' x ' + cm2m(this.width));
        }
        else {
            this.holder.find('.box_height span').text(cm2m(this.width));
        }
        this.resize();
    }
    changeColor(color) {
        this.color = color;
        this.holder.find('.core').css('background-color', this.color);
    }
    changeHeight(height) {
        this.height = parseInt(height);
        if (this.forceShowHeight) {
            this.holder.find('.box_width span').text(cm2m(this.height));
        }
    }
    changeLayer(value) {
        var newLayer = this.layer + parseInt(value);
        if (newLayer >= 10) {
            this.layer = newLayer;
            this.holder.css('z-index', newLayer);
        }
    }
    changeRadius(value) {
        this.radius = parseInt(value);
        this.holder.css({
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)'
        });
    }
    remove() {
        world.map.space.removeElmnt(this.uniqueId);
        this.holder.remove();
    }
    calcSafe(value) {
        return parseInt(value + '') + (2 * this.safe);
    }
    create() {
        var parent = this;
        this.holder = jQuery('<div>', {
            class: "obj",
        }).css({
            'width': world.scaleValue(this.calcSafe(this.length)),
            'height': world.scaleValue(this.calcSafe(this.width)),
            'left': world.scaleValue(this.left),
            'top': world.scaleValue(this.top),
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)',
            'z-index': this.layer,
            'padding': world.scaleValue(this.safe)
        }).append(jQuery('<div>', {
            class: 'core'
        }).css({
            'background-color': this.color
        }).append(jQuery('<div>', {
            class: 'objhandle'
        }).on('click', function (evt) {
            var $obj = $(this).closest('.obj');
            var state = $obj.hasClass('slcted');
            evt.ctrlKey ? null : $('.obj').removeClass('slcted');
            state ? $obj.removeClass('slcted') : $obj.addClass('slcted');
        }), jQuery('<div>', {
            class: "title disable-select"
        }).append(jQuery('<span>', {
            text: this.name
        })).css('font-size', world.scaleValue(30)), jQuery('<span>', {
            class: "removeme",
            html: '<i class="fa fa-remove" aria-hidden="true"></i>',
            title: "Usuń element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            $.confirm({
                title: 'Potwierdzenie usunięcia',
                content: 'Czy aby na pewno chcesz to zrobić?',
                buttons: {
                    'Usuń': function () {
                        parent.remove();
                    },
                    'Anuluj': function () {
                        return;
                    }
                }
            });
        }), jQuery('<span>', {
            class: "editme",
            html: '<i class="fa fa-pencil" aria-hidden="true"></i>',
            title: "Edytuj element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            world.elmntForEdit = parent;
            $('.obj').removeClass('editl');
            parent.holder.addClass('editl');
            $('.editobj form .col').css('display', 'none');
            $('.editobj form .col.rect').css('display', 'block');
            $('.editobj .rect input#objname').val(parent.name).data('original', parent.name);
            $('.editobj .rect input#objlength').val(parent.length).data('original', parent.length);
            $('.editobj .rect input#objwidth').val(parent.width).data('original', parent.width);
            $('.editobj .rect input.objheight').val(parent.height).data('original', parent.height);
            $('.editobj .rect input#objradius').val(parent.radius).data('original', parent.radius);
            $('.editobj .rect input#objcolor').val(parent.color).data('original', parent.color);
            $('.editobj .rect input#objsafe').val(parent.safe).data('original', parent.safe);
            $('.editobj .rect input#objshowdims').prop('checked', parent.showDims > 0).data('original', parent.showDims > 0);
            $('.menuView2').css('display', 'none');
            $('.menuView8').css('display', 'none');
            $('.menuView9').css('display', 'none');
            $('.menuView10').css('display', 'none');
            $('.menuView3').css('display', 'none');
            $('.menuView4').css('display', 'block');
        }), jQuery('<span>', {
            class: "rotateleft",
            html: '<i class="fa fa-rotate-left" aria-hidden="true"></i>',
            title: "Obróć w lewo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius - 1);
        }), jQuery('<span>', {
            class: "rotateright",
            html: '<i class="fa fa-rotate-right" aria-hidden="true"></i>',
            title: "Obróć w prawo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius + 1);
        }), jQuery('<span>', {
            class: "copyme",
            html: '<i class="fa fa-clone" aria-hidden="true"></i>',
            title: "Duplikuj",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            var snapshot = parent.snapshot;
            world.map.space.addElmnt(null, snapshot);
        }))).draggable({
            scroll: false,
            containment: ".map",
            cancel: '.title',
            stop: function () {
                var myPos = $(this).offset();
                var paPos = $(this).parent().offset();
                parent.top = world.realViewScale(myPos.top - paPos.top);
                parent.left = world.realViewScale(myPos.left - paPos.left);
            },
            drag: function (e, ui) {
                if (shiftIsPressed) {
                    ui.helper.clone().addClass('clonie').appendTo('.space').data('snapshot', parent.snapshot);
                    e.preventDefault();
                    return false;
                }
            }
        }).appendTo('.space');
        let box_width = jQuery('<div>', {
            class: "box_width disable-select" + (!this.showDims ? ' hdn' : '')
        });
        let box_height = jQuery('<div>', {
            class: "box_height disable-select" + (!this.showDims ? ' hdn' : '')
        });
        if (this.forceShowHeight) {
            box_width.append(jQuery('<span>', {
                text: cm2m(this.height)
            }));
            box_height.append(jQuery('<span>', {
                text: cm2m(this.length) + ' x ' + cm2m(this.width)
            }));
        }
        else {
            box_width.append(jQuery('<span>', {
                text: cm2m(this.length)
            }));
            box_height.append(jQuery('<span>', {
                text: cm2m(this.width)
            }));
        }
        this.holder.find('.core').prepend(box_width, box_height);
        this.resize();
    }
}
class Room {
    get div() {
        return this.holder;
    }
    get space() {
        return this.holder.find('.space');
    }
    constructor() {
        this.create();
    }
    centerPosition(width, height, left, top) {
        this.holder.css({
            'left': 'calc(50% - ' + (width / 2) + 'px + ' + (-Math.round(left)) + 'px - 10px)',
            'top': 'calc(50% - ' + (height / 2) + 'px + ' + (-Math.round(top)) + 'px - 10px)'
        });
    }
    dimensions(width = null, height = null) {
        if (width)
            this.holder.find('> .box_width span').text(cm2m(width));
        if (height)
            this.holder.find('> .box_height span').text(cm2m(height));
    }
    create() {
        this.holder = jQuery('<div>', {
            class: 'room'
        }).append(jQuery('<div>', {
            class: 'box_width disable-select'
        }).append(jQuery('<span>', {
            text: cm2m(0)
        })), jQuery('<div>', {
            class: 'box_height disable-select'
        }).append(jQuery('<span>', {
            text: cm2m(0)
        })));
    }
}
class Tables {
    get shapeText() {
        return 'tables';
    }
    get position() {
        return {
            top: this.top,
            left: this.left
        };
    }
    get div() {
        return this.holder;
    }
    get size() {
        return {
            width: this.totalWidth,
            length: this.totalLength,
            height: this.height
        };
    }
    get totalLength() {
        return ((this.fi + 100) * this.perRow) + ((this.perRow - 1) * this.between) + (2 * this.safe * this.perRow);
    }
    get totalWidth() {
        return ((this.fi + 100) * this.rows) + ((this.rows - 1) * this.aisle) + (2 * this.safe * this.rows);
    }
    get snapshot() {
        var obj = {
            shape: ELEMENT_TABLES,
            name: this.name,
            fi: this.fi,
            height: this.height,
            radius: this.radius,
            color: this.color,
            top: this.top,
            left: this.left,
            layer: this.layer,
            safe: this.safe,
            aisle: this.aisle,
            between: this.between,
            perRow: this.perRow,
            rows: this.rows,
            chairs: this.chairs,
            showDims: this.showDims
        };
        return obj;
    }
    get getUniqueId() {
        return this.uniqueId;
    }
    constructor(...properties) {
        this.uniqueId = Math.floor(Math.random() * 1000000);
        this.name = properties[0] || '';
        this.fi = properties[1] || 160;
        this.height = properties[2] || 100;
        this.top = properties[3] || 0;
        this.left = properties[4] || 0;
        this.color = properties[5] || '#ffffff';
        this.safe = properties[6] || 30;
        this.aisle = properties[7] || 50;
        this.between = properties[8] || 50;
        this.perRow = properties[9] || 2;
        this.rows = properties[10] || 2;
        this.chairs = properties[11] || 8;
        this.radius = properties[12] || 0;
        this.layer = properties[13] || 10;
        this.showDims = properties[14] || 0;
        this.create();
    }
    scale(calc) {
        var scl = ((this.totalLength / 9) + (this.totalWidth / 9)) / 2;
        this.holder.find('.title').css('font-size', calc(scl));
        this.holder.find('.box_height').css('width', calc(this.totalWidth));
        let smlr = Math.min(this.totalLength, this.totalWidth);
        let fntsize = smlr / 12;
        if (calc(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (calc(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': calc(fntsize),
            'top': -1 * calc(fntsize + calc(fntsize))
        });
        this.holder.find('.chair').css({
            'height': calc(50),
            'width': calc(50)
        });
        this.holder.find('.rowie:not(:first-child) .table-safe').css({
            'margin-top': calc(this.aisle)
        });
        this.holder.find('.table-safe:not(:last-child)').css({
            'margin-right': calc(this.between)
        });
        this.holder.css({
            'width': calc(this.totalLength),
            'height': calc(this.totalWidth),
            'top': calc(this.top),
            'left': calc(this.left)
        });
        this.holder.find('.table-safe').css({
            'flex-basis': calc(this.fi + 100 + (2 * this.safe)),
            'height': calc(this.fi + 100 + (2 * this.safe)),
            'padding-left': calc(this.safe),
            'padding-top': calc(this.safe)
        });
        this.holder.find('.tabl').css({
            'width': calc(this.fi + 100),
            'height': calc(this.fi + 100)
        });
        this.holder.find('.tabl > div').css({
            'width': calc(this.fi),
            'height': calc(this.fi),
            'margin-top': calc(50),
            'margin-left': calc(50)
        });
        switch (this.chairs) {
            case 8:
                this.holder.find('.chair:nth-child(1)').css({
                    'left': 'calc(-' + calc(20) + 'px - 1px)',
                    'top': 'calc(-' + calc(20) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(2)').css({
                    'right': 'calc(-' + calc(20) + 'px - 1px)',
                    'bottom': 'calc(-' + calc(20) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(3)').css({
                    'right': 'calc(-' + calc(20) + 'px - 1px)',
                    'top': 'calc(-' + calc(20) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(4)').css({
                    'left': 'calc(-' + calc(20) + 'px - 1px)',
                    'bottom': 'calc(-' + calc(20) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(5)').css({
                    'left': 'calc(-' + calc(50) + 'px - 1px)',
                    'top': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(6)').css({
                    'right': 'calc(-' + calc(50) + 'px - 1px)',
                    'top': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(7)').css({
                    'top': 'calc(-' + calc(50) + 'px - 1px)',
                    'left': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(8)').css({
                    'bottom': 'calc(-' + calc(50) + 'px - 1px)',
                    'left': 'calc(50% - ' + calc(25) + 'px)'
                });
                break;
            case 10:
                this.holder.find('.chair:nth-child(1)').css({
                    'top': 'calc(-' + calc(50) + 'px - 1px)',
                    'left': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(2)').css({
                    'left': 'calc(-' + calc(8) + 'px - 1px)',
                    'top': 'calc(-' + calc(31) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(3)').css({
                    'left': 'calc(-' + calc(47) + 'px - 1px)',
                    'top': 'calc(' + calc(22) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(4)').css({
                    'left': 'calc(-' + calc(47) + 'px - 1px)',
                    'bottom': 'calc(' + calc(22) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(5)').css({
                    'left': 'calc(-' + calc(8) + 'px - 1px)',
                    'bottom': 'calc(-' + calc(31) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(6)').css({
                    'bottom': 'calc(-' + calc(50) + 'px - 1px)',
                    'left': 'calc(50% - ' + calc(25) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(7)').css({
                    'right': 'calc(-' + calc(8) + 'px - 1px)',
                    'bottom': 'calc(-' + calc(31) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(8)').css({
                    'right': 'calc(-' + calc(47) + 'px - 1px)',
                    'bottom': 'calc(' + calc(22) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(9)').css({
                    'right': 'calc(-' + calc(47) + 'px - 1px)',
                    'top': 'calc(' + calc(22) + 'px - 1px)'
                });
                this.holder.find('.chair:nth-child(10)').css({
                    'right': 'calc(-' + calc(8) + 'px - 1px)',
                    'top': 'calc(-' + calc(31) + 'px - 1px)'
                });
                break;
            case 12:
                this.holder.find('.chair:nth-child(1)').css({
                    'top': calc(-53),
                    'left': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(2)').css({
                    'left': calc(6),
                    'top': calc(-37)
                });
                this.holder.find('.chair:nth-child(3)').css({
                    'left': calc(-37),
                    'top': calc(5)
                });
                this.holder.find('.chair:nth-child(4)').css({
                    'left': calc(-53),
                    'bottom': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(5)').css({
                    'left': calc(-37),
                    'bottom': calc(5)
                });
                this.holder.find('.chair:nth-child(6)').css({
                    'left': calc(6),
                    'bottom': calc(-37)
                });
                this.holder.find('.chair:nth-child(7)').css({
                    'bottom': calc(-53),
                    'left': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(8)').css({
                    'right': calc(6),
                    'bottom': calc(-37)
                });
                this.holder.find('.chair:nth-child(9)').css({
                    'right': calc(-37),
                    'bottom': calc(5)
                });
                this.holder.find('.chair:nth-child(10)').css({
                    'right': calc(-53),
                    'bottom': 'calc(50% - ' + calc(25) + 'px)'
                });
                this.holder.find('.chair:nth-child(11)').css({
                    'right': calc(-37),
                    'top': calc(5)
                });
                this.holder.find('.chair:nth-child(12)').css({
                    'right': calc(6),
                    'top': calc(-37)
                });
                break;
        }
    }
    changePosition(top, left) {
        this.top = parseInt(top);
        this.left = parseInt(left);
        this.holder.css('top', world.scaleValue(this.calcSafe(this.top)));
        this.holder.css('left', world.scaleValue(this.calcSafe(this.left)));
    }
    changeName(name) {
        this.name = name;
        this.holder.find('.title span').text(name);
    }
    changeFi(fi = null) {
        if (fi != null) {
            this.fi = parseInt(fi);
            this.resize();
        }
        if (this.fi == 160 && this.chairs == 12 || this.fi == 180 && this.chairs == 8)
            this.chairs = 10;
        this.rebuild();
    }
    changeChairs(chairs) {
        if (chairs == null)
            return;
        this.chairs = parseInt(chairs);
        if (this.chairs == 8 && this.fi == 180) {
            this.fi = 160;
        }
        else if (this.chairs == 12 && this.fi == 160) {
            this.fi = 180;
        }
        this.rebuild();
    }
    changeSafe(safe = null) {
        if (safe != null) {
            this.safe = parseInt(safe);
            this.resize();
        }
        this.holder.find('.table-safe').css({
            'flex-basis': world.scaleValue(this.fi + 100 + (2 * this.safe)),
            'height': world.scaleValue(this.fi + 100 + (2 * this.safe)),
            'padding-left': world.scaleValue(this.safe),
            'padding-top': world.scaleValue(this.safe)
        });
    }
    changeAisle(aisle = null) {
        if (aisle != null) {
            this.aisle = parseInt(aisle);
            this.resize();
        }
        this.holder.find('.rowie:not(:first-child) .table-safe').css({
            'margin-top': world.scaleValue(this.aisle)
        });
    }
    changeShowDims(show) {
        this.showDims = parseInt(show);
        if (this.showDims == 0) {
            this.holder.find('.box_width').addClass('hdn');
            this.holder.find('.box_height').addClass('hdn');
        }
        else {
            this.holder.find('.box_width').removeClass('hdn');
            this.holder.find('.box_height').removeClass('hdn');
        }
    }
    changeBetween(between = null) {
        if (between != null) {
            this.between = parseInt(between);
            this.resize();
        }
        this.holder.find('.table-safe:not(:last-child)').css({
            'margin-right': world.scaleValue(this.between)
        });
    }
    resize() {
        this.holder.css({
            'width': world.scaleValue(this.totalLength),
            'height': world.scaleValue(this.totalWidth)
        });
        this.holder.find('.box_height').css('width', world.scaleValue(this.totalWidth));
        let smlr = Math.min(this.totalLength, this.totalWidth);
        let fntsize = smlr / 12;
        if (world.scaleValue(fntsize) > 15)
            fntsize = world.realViewScale(15);
        if (world.scaleValue(fntsize) < 9)
            fntsize = world.realViewScale(9);
        this.holder.find('.box_height span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
        this.holder.find('.box_width span').css({
            'font-size': world.scaleValue(fntsize),
            'top': -1 * world.scaleValue(fntsize + world.scaleValue(fntsize))
        });
        var scl = ((this.totalLength / 9) + (this.totalWidth / 9)) / 2;
        this.holder.find('.title').css('font-size', world.scaleValue(scl));
        this.holder.find('.core > .box_width span').text(cm2m(this.totalLength));
        this.holder.find('.core > .box_height span').text(cm2m(this.totalWidth));
    }
    changeRows(rows) {
        this.rows = parseInt(rows);
        this.rebuild();
    }
    changePerRow(perRow) {
        this.perRow = parseInt(perRow);
        this.rebuild();
    }
    rebuild() {
        var $tables = this.holder.find('.tables').text('');
        for (var i = 0; i < this.rows; i++) {
            var $row = jQuery('<div>', {
                class: 'rowie'
            }).appendTo($tables);
            for (var j = 0; j < this.perRow; j++) {
                var $table = jQuery('<div>', {
                    class: 'table-safe'
                }).append(jQuery('<div>', {
                    class: 'tabl table-' + this.fi
                }).css({
                    'width': world.scaleValue(this.fi + 100),
                    'height': world.scaleValue(this.fi + 100)
                }).append(jQuery('<div>', {
                    class: 'table-' + this.fi + '-' + this.chairs
                }).css({
                    'width': world.scaleValue(this.fi),
                    'height': world.scaleValue(this.fi),
                    'margin-top': world.scaleValue(50),
                    'margin-left': world.scaleValue(50)
                }))).appendTo($row);
                var addhere = $table.find('.tabl div');
                for (var k = 1; k <= this.chairs; k++) {
                    var $chair = jQuery('<div>', {
                        class: 'chair'
                    }).css({
                        'width': world.scaleValue(50),
                        'height': world.scaleValue(50)
                    }).appendTo(addhere);
                    switch (k) {
                        case 1:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'left': 'calc(-' + world.scaleValue(20) + 'px - 1px)',
                                        'top': 'calc(-' + world.scaleValue(20) + 'px - 1px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'top': 'calc(-' + world.scaleValue(50) + 'px - 1px)',
                                        'left': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'top': world.scaleValue(-53),
                                        'left': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                            }
                            break;
                        case 2:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(20) + 'px - 1px)',
                                        'bottom': 'calc(-' + world.scaleValue(20) + 'px - 1px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'left': 'calc(-' + world.scaleValue(8) + 'px - 1px)',
                                        'top': 'calc(-' + world.scaleValue(31) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'top': world.scaleValue(-37),
                                        'left': world.scaleValue(6)
                                    });
                                    break;
                            }
                            break;
                        case 3:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(20) + 'px - 1px)',
                                        'top': 'calc(-' + world.scaleValue(20) + 'px - 1px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        '-ms-transform': 'rotate(-72deg)',
                                        '-webkit-transform': 'rotate(-72deg)',
                                        'transform': 'rotate(-72deg)',
                                        'left': 'calc(-' + world.scaleValue(47) + 'px - 1px)',
                                        'top': 'calc(' + world.scaleValue(22) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'top': world.scaleValue(5),
                                        'left': world.scaleValue(-37)
                                    });
                                    break;
                            }
                            break;
                        case 4:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'left': 'calc(-' + world.scaleValue(20) + 'px - 1px)',
                                        'bottom': 'calc(-' + world.scaleValue(20) + 'px - 1px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'left': 'calc(-' + world.scaleValue(47) + 'px - 1px)',
                                        'bottom': 'calc(' + world.scaleValue(22) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'bottom': 'calc(50% - ' + world.scaleValue(25) + 'px)',
                                        'left': world.scaleValue(-53)
                                    });
                                    break;
                            }
                            break;
                        case 5:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'left': 'calc(-' + world.scaleValue(50) + 'px - 1px)',
                                        'top': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'left': 'calc(-' + world.scaleValue(8) + 'px - 1px)',
                                        'bottom': 'calc(-' + world.scaleValue(31) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'bottom': world.scaleValue(5),
                                        'left': world.scaleValue(-37)
                                    });
                                    break;
                            }
                            break;
                        case 6:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(50) + 'px - 1px)',
                                        'top': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'bottom': 'calc(-' + world.scaleValue(50) + 'px - 1px)',
                                        'left': 'calc(50% - ' + world.scaleValue(25) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'bottom': world.scaleValue(-37),
                                        'left': world.scaleValue(6)
                                    });
                                    break;
                            }
                            break;
                        case 7:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'top': 'calc(-' + world.scaleValue(50) + 'px - 1px)',
                                        'left': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(8) + 'px - 1px)',
                                        'bottom': 'calc(-' + world.scaleValue(31) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'left': 'calc(50% - ' + world.scaleValue(25) + 'px)',
                                        'bottom': world.scaleValue(-53)
                                    });
                                    break;
                            }
                            break;
                        case 8:
                            switch (this.chairs) {
                                case 8:
                                    $chair.css({
                                        'bottom': 'calc(-' + world.scaleValue(50) + 'px - 1px)',
                                        'left': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                                case 10:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(47) + 'px - 1px)',
                                        'bottom': 'calc(' + world.scaleValue(22) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'right': world.scaleValue(6),
                                        'bottom': world.scaleValue(-37)
                                    });
                                    break;
                            }
                            break;
                        case 9:
                            switch (this.chairs) {
                                case 10:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(47) + 'px - 1px)',
                                        'top': 'calc(' + world.scaleValue(22) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'bottom': world.scaleValue(6),
                                        'right': world.scaleValue(-37)
                                    });
                                    break;
                            }
                            break;
                        case 10:
                            switch (this.chairs) {
                                case 10:
                                    $chair.css({
                                        'right': 'calc(-' + world.scaleValue(8) + 'px - 1px)',
                                        'top': 'calc(-' + world.scaleValue(31) + 'px - 1px)'
                                    });
                                    break;
                                case 12:
                                    $chair.css({
                                        'right': world.scaleValue(-53),
                                        'bottom': 'calc(50% - ' + world.scaleValue(25) + 'px)'
                                    });
                                    break;
                            }
                            break;
                        case 11:
                            switch (this.chairs) {
                                case 12:
                                    $chair.css({
                                        'right': world.scaleValue(-37),
                                        'top': world.scaleValue(5)
                                    });
                                    break;
                            }
                            break;
                        case 12:
                            switch (this.chairs) {
                                case 12:
                                    $chair.css({
                                        'right': world.scaleValue(6),
                                        'top': world.scaleValue(-37)
                                    });
                                    break;
                            }
                            break;
                    }
                }
                jQuery('<div>', {
                    class: 'table-top'
                }).css({
                    'background-color': this.color
                })
                    .appendTo(addhere);
                this.resize();
            }
        }
        this.changeSafe();
        this.changeAisle();
        this.changeBetween();
        this.resize();
    }
    changeColor(color) {
        this.color = color;
        this.holder.find('.table-top').css('background-color', this.color);
    }
    changeHeight(height) {
        this.height = parseInt(height);
    }
    changeLayer(value) {
        var newLayer = this.layer + parseInt(value);
        if (newLayer >= 10) {
            this.layer = newLayer;
            this.holder.css('z-index', newLayer);
        }
    }
    changeRadius(value) {
        this.radius = parseInt(value);
        this.holder.css({
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)'
        });
    }
    remove() {
        world.map.space.removeElmnt(this.uniqueId);
        this.holder.remove();
    }
    calcSafe(value) {
        return parseInt(value + '') + (2 * this.safe);
    }
    create() {
        var parent = this;
        this.holder = jQuery('<div>', {
            class: "obj t",
        }).css({
            'width': world.scaleValue(parent.totalLength),
            'height': world.scaleValue(parent.totalWidth),
            'left': world.scaleValue(this.left),
            'top': world.scaleValue(this.top),
            'transform': 'rotate(' + this.radius + 'deg)',
            '-webkit-transform': 'rotate(' + this.radius + 'eg)',
            '-moz-transform': 'rotate(' + this.radius + 'deg)',
            '-o-transform': 'rotate(' + this.radius + 'deg)',
            'z-index': this.layer
        })
            .append(jQuery('<div>', {
            class: 'core'
        }).css({
            'border': 'none'
        })
            .append(jQuery('<div>', {
            class: 'objhandle'
        }).on('click', function (evt) {
            var $obj = $(this).closest('.obj');
            var JPaprocki = $obj.hasClass('slcted');
            evt.ctrlKey ? null : $('.obj').removeClass('slcted');
            JPaprocki ? $obj.removeClass('slcted') : $obj.addClass('slcted');
        }), jQuery('<div>', {
            class: 'tables'
        }), jQuery('<div>', {
            class: "box_width disable-select" + (!this.showDims ? ' hdn' : '')
        })
            .append(jQuery('<span>', {
            text: cm2m(parent.totalLength)
        })), jQuery('<div>', {
            class: "box_height disable-select" + (!this.showDims ? ' hdn' : '')
        })
            .append(jQuery('<span>', {
            text: cm2m(parent.totalWidth)
        })), jQuery('<div>', {
            class: "title disable-select"
        }).append(jQuery('<span>', {
            text: this.name
        })).css('font-size', world.scaleValue(40)), jQuery('<span>', {
            class: "removeme",
            html: '<i class="fa fa-remove" aria-hidden="true"></i>',
            title: "Usuń element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            $.confirm({
                title: 'Potwierdzenie usunięcia',
                content: 'Czy aby na pewno chcesz to zrobić?',
                buttons: {
                    'Usuń': function () {
                        parent.remove();
                    },
                    'Anuluj': function () {
                        return;
                    }
                }
            });
        }), jQuery('<span>', {
            class: "editme",
            html: '<i class="fa fa-pencil" aria-hidden="true"></i>',
            title: "Edytuj element",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function (evt) {
            world.elmntForEdit = parent;
            $('.obj').removeClass('editl');
            parent.holder.addClass('editl');
            $('.editobj form .col').css('display', 'none');
            $('.editobj form .col.tables').css('display', 'block');
            $('.editobj .tables input#objname').val(parent.name).data('original', parent.name);
            $('.editobj .tables input#objfi').val(parent.fi).data('original', parent.fi);
            $('.editobj .tables input.objheight').val(parent.height).data('original', parent.height);
            $('.editobj .tables input#objradius').val(parent.radius).data('original', parent.radius);
            $('.editobj .tables input#objcolor').val(parent.color).data('original', parent.color);
            $('.editobj .tables input#objsafe').val(parent.safe).data('original', parent.safe);
            $('.editobj .tables input#objrows').val(parent.rows).data('original', parent.rows);
            $('.editobj .tables input#objperrow').val(parent.perRow).data('original', parent.perRow);
            $('.editobj .tables input#objaisle').val(parent.aisle).data('original', parent.aisle);
            $('.editobj .tables input#objbetween').val(parent.between).data('original', parent.between);
            $('.editobj .tables input#objshowdims').prop('checked', parent.showDims > 0).data('original', parent.showDims > 0);
            $('.editobj .objchrs').prop('checked', false).data('original', false);
            $('.editobj .objchrs[data-value="' + parent.chairs + '"]').prop('checked', true).data('original', true);
            $('.editobj #fis input').prop('checked', false).data('original', false);
            $('.editobj #fis label[data-value="' + parent.fi + '"] input').prop('checked', true).data('original', true);
            $('.menuView2').css('display', 'none');
            $('.menuView8').css('display', 'none');
            $('.menuView9').css('display', 'none');
            $('.menuView10').css('display', 'none');
            $('.menuView3').css('display', 'none');
            $('.menuView4').css('display', 'block');
        }), jQuery('<span>', {
            class: "rotateleft",
            html: '<i class="fa fa-rotate-left" aria-hidden="true"></i>',
            title: "Obróć w lewo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius - 1);
        }), jQuery('<span>', {
            class: "rotateright",
            html: '<i class="fa fa-rotate-right" aria-hidden="true"></i>',
            title: "Obróć w prawo o 1 stopień",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            parent.changeRadius(parent.radius + 1);
        }), jQuery('<span>', {
            class: "copyme",
            html: '<i class="fa fa-clone" aria-hidden="true"></i>',
            title: "Duplikuj",
            'data-placement': 'right',
            'data-trigger': 'hover'
        }).tooltip('show').on('click', function () {
            var snapshot = parent.snapshot;
            world.map.space.addElmnt(null, snapshot);
        }))).draggable({
            scroll: false,
            containment: ".map",
            cancel: '.title',
            stop: function () {
                var myPos = $(this).offset();
                var paPos = $(this).parent().offset();
                parent.top = world.realViewScale(myPos.top - paPos.top);
                parent.left = world.realViewScale(myPos.left - paPos.left);
            },
            drag: function (e, ui) {
                if (shiftIsPressed) {
                    ui.helper.clone().addClass('clonie').appendTo('.space').data('snapshot', parent.snapshot);
                    e.preventDefault();
                    return false;
                }
            }
        }).appendTo('.space');
        this.rebuild();
    }
}
class World {
    constructor(name, desc, id = null, map = null) {
        this.flagSpaceIsTooSmall = false;
        this.flagSpaceIsTooBig = false;
        if (name.length) {
            this.projectName = name;
        }
        else {
            let nmbr = 1;
            $('.projectlist a h5 span').each((i, e) => {
                if ($(e).text().match(/Bez tytułu \(\d+\)/)) {
                    let curr = parseInt($(e).text().match(/\d+/)[0]);
                    console.log(curr);
                    if (curr >= nmbr)
                        nmbr = curr + 1;
                }
            });
            this.projectName = "Bez tytułu (" + nmbr + ")";
        }
        this.projectDesc = desc || 'Brak';
        this.projectId = id;
        this.viewScale = 0;
        this.create(map);
    }
    get div() {
        return this.holder;
    }
    set setId(id) {
        this.projectId = id;
    }
    get snapshot() {
        var obj = {
            projectName: this.projectName,
            projectDesc: this.projectDesc,
            projectId: this.projectId
        };
        return obj;
    }
    get map() {
        return this.mapInstance;
    }
    get name() {
        return this.projectName;
    }
    get id() {
        return this.projectId;
    }
    get description() {
        return this.projectDesc;
    }
    get viewScalePercent() {
        return (this.viewScale + 100) / 100;
    }
    set newViewScale(vs) {
        this.viewScale = vs;
        this.zoom();
    }
    prepareToSave() {
        var obj = {
            world: {
                snapshot: this.snapshot,
                map: {
                    snapshot: this.mapInstance.snapshot,
                    space: this.mapInstance.space.snapshot
                }
            }
        };
        return obj;
    }
    addLineCord(x, y) {
        switch (lineMode) {
            case 1:
                this.lineForEdit = this.map.space.addLine(world.realViewScale(x), world.realViewScale(y));
                lineMode = 2;
                break;
            case 2:
                $('.map-line').removeClass('btn-success');
                this.lineForEdit = null;
                lineMode = 0;
                $('.map-measure').removeClass('disabled');
                return;
            case 3:
                if (world.lineForEdit != undefined)
                    world.map.space.removeLine(world.lineForEdit.id);
                this.lineForEdit = this.map.space.addLine(world.realViewScale(x), world.realViewScale(y));
                this.lineForEdit.temp = true;
                lineMode = 4;
                break;
            case 4:
                lineMode = 3;
                return;
        }
    }
    remove() {
        this.holder.remove();
    }
    adjust() {
        var visibleWidth = $('#page-content-wrapper').width();
        var visibleHeight = $('#page-content-wrapper').height();
        var sSize = this.mapInstance.space.size;
        var left = 0;
        var top = 0;
        var bottom = sSize.width;
        var right = sSize.length;
        $.each(this.mapInstance.space.elmnts, (i, e) => {
            var pos = e.variant.position;
            var size = e.variant.size;
            if (pos.left < left)
                left = pos.left;
            if (pos.top < top)
                top = pos.top;
            if (pos.top + size.width > bottom)
                bottom = pos.top + size.width;
            if (pos.left + size.length > right)
                right = pos.left + size.length;
        });
        var length = right - left;
        var width = bottom - top;
        var adjustedWidth = (visibleWidth * 0.9) / length;
        var adjustedHeight = (visibleHeight * 0.9) / width;
        var scale = (Math.min(adjustedWidth, adjustedHeight) - 1) * 100;
        world.newViewScale = scale;
        this.mapInstance.centerPosition();
        this.mapInstance.space.room.centerPosition(world.scaleValue(length), world.scaleValue(width), world.scaleValue(left), world.scaleValue(top));
    }
    viewScale_plus() {
        let currWidth = this.mapInstance.space.div.width();
        let currHeight = this.mapInstance.space.div.height();
        if (currWidth <= 2000 && currHeight <= 2000) {
            this.viewScale += VIEWSCALESTEP;
            this.zoom();
        }
        else {
            $('.map-plus').addClass('disabled');
        }
        $('.map-minus').removeClass('disabled');
    }
    viewScale_minus() {
        let currWidth = this.mapInstance.space.div.width();
        let currHeight = this.mapInstance.space.div.height();
        if (currWidth >= 200 && currHeight >= 200) {
            this.viewScale -= VIEWSCALESTEP;
            this.zoom();
        }
        else {
            $('.map-minus').addClass('disabled');
        }
        $('.map-plus').removeClass('disabled');
    }
    scaleValue(value) {
        return value * this.viewScalePercent;
    }
    realViewScale(value) {
        return value / this.viewScalePercent;
    }
    zoom() {
        var parent = this;
        this.mapInstance.space.scale(function (value) {
            return parent.scaleValue(value);
        });
    }
    create(map = null) {
        var width = map ? map.width : null;
        var height = map ? map.height : null;
        var space = map ? map.space : null;
        this.mapInstance = new Mapz(width, height, space);
        this.holder = jQuery('<div>', {
            class: 'world'
        }).append(this.mapInstance.div);
        $.onCreate('div.space', () => {
            this.adjust();
        });
    }
}
//# sourceMappingURL=spaceplanner.js.map