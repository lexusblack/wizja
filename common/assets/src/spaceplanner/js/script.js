if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
    var msViewportStyle = document.createElement('style')
    msViewportStyle.appendChild(
        document.createTextNode(
            '@-ms-viewport{width:auto!important}'
        )
    )
    document.head.appendChild(msViewportStyle)
}

String.prototype.escapeDiacritics = function() {
    return this.replace(/ą/g, 'a').replace(/Ą/g, 'A')
        .replace(/ć/g, 'c').replace(/Ć/g, 'C')
        .replace(/ę/g, 'e').replace(/Ę/g, 'E')
        .replace(/ł/g, 'l').replace(/Ł/g, 'L')
        .replace(/ń/g, 'n').replace(/Ń/g, 'N')
        .replace(/ó/g, 'o').replace(/Ó/g, 'O')
        .replace(/ś/g, 's').replace(/Ś/g, 'S')
        .replace(/ż/g, 'z').replace(/Ż/g, 'Z')
        .replace(/ź/g, 'z').replace(/Ź/g, 'Z');
}

$(() => {

    $('#acceptsendemail').on('click', () => {
        let email = $('#emailo').val();
        let topic = $('#topico').val();
        let descr = $('#descro').val();
        let ispdf = $('#ispdfo');

        if (email.length < 1) {
            return;
        }

        if (topic.length < 1) {
            topic = world.projectName;
        }

        var data = new FormData();
        data.append("email", email);
        data.append("topic", topic);
        data.append("descr", descr);

        if (ispdf.prop('checked')) {
            $("#wrapper").removeClass("toggled")
            world.adjust();

            roomToPDF((doc) => {
                var pdf = doc.output();
                data.append("pdf", pdf);
                sendEmail(data);
            })
        } else {
            sendEmail(data);
        }

        $('#sendemail').modal('hide');
    })

    $('.map-pdf').on('click', () => {
        if ($('button.map-minus').hasClass('disabled')) return;
        $("#wrapper").removeClass("toggled")
        world.adjust();

        roomToPDF((doc) => {
            doc.save('plan.pdf');
        })
    })
});

var sendEmail = (data) => {
    xhr = new XMLHttpRequest();

    xhr.open('POST', emailUrl, true);
    xhr.onreadystatechange = function(response) {
        // console.log(response)
    };
    xhr.send(data);
}

var roomToPDF = (cb) => {
    if (lineMode > 2 && world.lineForEdit != undefined)
        world.map.space.removeLine(world.lineForEdit.id);

    html2canvas($(".room"), {
        onrendered: function(canvas) {

            var base64 = canvas.toDataURL("image/jpeg");

            if (!base64) {
                return;
            }

            var doc = new jsPDF();
            
            getImageUri(logo1Url, (dataUri, width, height) => {
                let scale = Math.min(40 / width, 15 / height);
                doc.addImage(dataUri, 'JPEG', 15, 15, width * scale, height * scale);

                getImageUri(logo2Url, (dataUri, width, height) => {
                    let scale = Math.min(40 / width, 15 / height);
                    doc.addImage(dataUri, 'JPEG', 190 - (width * scale), 280 - (height * scale), width * scale, height * scale);

                    doc.setFontSize(14);
                    doc.setFont("monospace");
                    doc.lstext(world.name.escapeDiacritics(), 15, 50, 3);
                    doc.setFontSize(9);
                    doc.lstext('Wyemitowano z programu Space Planner przez newsystems.pl', 15, 280, 2);

                    if (canvas.width > canvas.height) {
                        let scale = Math.min(185 / canvas.width, 180 / canvas.height);
                        rotateBase64Image90deg(base64, true, (nwBase64) => {
                            doc.addImage(nwBase64, 'JPEG', 15, 60, canvas.height * scale, canvas.width * scale);
                            cb(doc);
                        });
                    } else {
                        let scale = Math.min(185 / canvas.height, 180 / canvas.width);
                        doc.addImage(base64, 'JPEG', 15, 60, canvas.width * scale, canvas.height * scale);
                        cb(doc);
                    }
                });
            });
        }
    });
}

function imgToBase64(id, cb) {
    var c = document.createElement('canvas');
    var ctx = c.getContext('2d');
    var img = document.getElementById(id);
    ctx.drawImage(img, 10, 10);
    cb(c.toDataURL(), img.width, img.height);
}

function getImageUri(url, cb) {
    $.ajax({
        method: 'POST',
        url: url,
        success: (data) => {
            var image = new Image();
            image.setAttribute('crossOrigin', 'anonymous'); //getting images from external domain

            image.onload = function() {
                var canvas = document.createElement('canvas');
                canvas.width = this.naturalWidth;
                canvas.height = this.naturalHeight;

                //next three lines for white background in case png has a transparent background
                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff'; /// set white fill style
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                canvas.getContext('2d').drawImage(this, 0, 0);

                cb(canvas.toDataURL('image/jpeg'), canvas.width, canvas.height);
            };

            image.src = data;
        }
    })
}

window.onbeforeunload = function() {
    if (world) {
        if (typeof(Storage) !== "undefined") {
            if (lineMode > 2 && world.lineForEdit != undefined)
                world.map.space.removeLine(world.lineForEdit.id);

            let snapshot = world.prepareToSave();
            let userid = Cookies.get('userid');

            if (userid) {
                snapshot = snapshot.userid = userid;
                localStorage.setItem("backup", JSON.stringify(snapshot));
            }

        } else {
            console.log('Sorry! No Web Storage support..');
        }
    }
};

function rotateBase64Image90deg(base64Image, isClockwise, cb) {
    // create an off-screen canvas
    var offScreenCanvas = document.createElement('canvas');
    offScreenCanvasCtx = offScreenCanvas.getContext('2d');

    // cteate Image
    var img = new Image();
    img.src = base64Image;
    img.onload = () => {

        // set its dimension to rotated size
        offScreenCanvas.height = img.width;
        offScreenCanvas.width = img.height;

        // rotate and draw source image into the off-screen canvas:
        if (isClockwise) {
            offScreenCanvasCtx.rotate(90 * Math.PI / 180);
            offScreenCanvasCtx.translate(0, -offScreenCanvas.width);
        } else {
            offScreenCanvasCtx.rotate(-90 * Math.PI / 180);
            offScreenCanvasCtx.translate(-offScreenCanvas.height, 0);
        }
        offScreenCanvasCtx.drawImage(img, 0, 0);

        // encode image to data-uri with base64
        cb(offScreenCanvas.toDataURL("image/jpeg", 100));
    }

}