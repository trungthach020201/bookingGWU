// Admin
// $.ajax({
//     type: 'post',
//     url: '/data',
//     data: {
//         date: {}
//     },
//     success: function(data) {
//         $.each(data, function(index, row) {
//             console.log(row)
//             $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').empty()
//             let s = ''
//             if ($.isNumeric(row.class)) {
//                 s += row.room_id
//                 if (row.class == '0') {
//                     s += '<span class="text-danger">Processing</span>'
//                 } else {
//                     s += '<span class="text-success">Accept</span>'
//                 }
//             } else {
//                 s += row.class
//             }
//             $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').append(s)
//         })
//     }
// })
$.ajax({
    type: 'POST',
    url: '/reservation',
    data: {
        day: day,
        from: 1,
        to: 22
    },
    success: function(data) {
        // console.log(data);
        $('#request-zone').empty()
        $.each(data, function(index, row) {
            // console.log(row)
            $('#request-zone').append(
            '<div class="card" style="border: 2px solid #003399;">' + 
            '<h5 class="card-header" style="font-weight: 600; background-color: #003399; color: #ffffff;">Request</h5>' + 
            '<div class="card-body row">' + 
            '<div class="card_content col-md-10">' + 
            '<h6 class="card-title">' + 
                '<span>Student Name:</span>' + row.fullname + 
            '</h6>' + 
            '<h6 class="card-title">' + 
                '<span>Student Id:</span>' + row.mssv_cb + 
            '</h6>' + 
            '<h6 class="card-title">' + 
                '<span>Room:</span>' + row.name + 
            '</h6>' + 
            '<h6 class="card-title">' + 
                '<span>Date:</span>' + row.date + 
            '</h6>' + 
            '<h6 class="card-title">' + 
                '<span>Slot:</span>' + row.slot + 
            '</h6>' + 
            '<h6 class="card-text">' + 
                '<span>Reason:</span>' + row.reason + 
            '</h6>' + 
            '</div>' + 
            '<div id="' + row.id + '" class="'+ row.slot + row.name +' card_button col-md-2">' + 
            '<button class="button accept btnaccept" style=" border: none;" >'+
                '<a class="text-decoration-none text-light">Accept</a>'+
            '</button>' + 
            '<button data-bs-toggle="modal" data-bs-target="#exampleModal"  class="button recject btnreject" style="background-color: #D2001A; border: #D2001A;>"'+
                '<a class="text-decoration-none text-light" onclick="getIdReject(' + row.id + ',\''+ row.slot + row.name +'\')">Reject</a>' + 
            '</button>' + 
            '</div>' + 
            '</div>' + 
            '</div>')
        })

        $('.btnaccept').click(function() {
            var parent = $(this).parent();
            parent.empty().append('<div class="loader"><div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div></div>');
            var idRe = parent.attr('id');
            var sameRe = parent.attr('class').split(' ')[0];
            $('div.'+sameRe).empty().append('<div class="loader"><div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div></div>');
            console.log(idRe + sameRe);

            $.ajax({
                type: "GET",
                url: "/accept",
                data: {
                    id: idRe
                },
                success: function (response) {
                    parent.parent().parent().replaceWith();
                    $('div.'+sameRe).parent().parent().replaceWith();
                    console.log('Success');

                    $.ajax({
                        type: 'post',
                        url: '/data',
                        data: {
                            day: day,
                            from: 1,
                            to: 25
                        },
                        success: function(data) {
                            // console.log(data);
                            // console.log(day);
                            $.each(data, function(index, row) {
                                // console.log(row)
                                $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').empty()
                                let s = ''
                                if ($.isNumeric(row.class)) {
                                    s += '<span>' + row.room_id + '</span>';
                                    s += '<span>' + row.mssv + '</span>';
                                    if (row.class == '0') {
                                        s += '<span class="text-danger">Processing</span>'
                                    } else {
                                        s += '<span class="text-success">Accept</span>'
                                    }
                                } else {
                                    s += row.class
                                }
                                $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').append(s)
                            })
                        }
                    })

                }
            });
        });

        // $('.btnreject').click(function() {
        //     var parent = $(this).parent();
        //     parent.empty().append('<div class="loader"><div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div></div>');
        //     var sameRe = parent.attr('class').split(' ')[0];
        //     $('div.'+sameRe).empty().append('<div class="loader"><div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div></div>');
        // });
    }
})

function getIdReject(rowId, sameE) {
    var id = document.getElementById('rowId');
    id.value = rowId;

    var same = document.getElementById('same');
    same.value = sameE;
}

function checkSubmit() {
    if (document.getElementById('txtReason').value == "") {
        alert("Opps");
    } else {
        var idRe = document.getElementById('rowId').value
        var reason = document.getElementById('txtReason').value;
        var sameRe = document.getElementById('same').value;
        $('div.'+sameRe).empty().append('<div class="loader"><div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div></div>');
        $.ajax({
            type: 'post',
            url: '/reject',
            data: {
                id:idRe,
                txtReason: reason
            },
            success: function() {
                console.log("Success");
                $('div.'+sameRe).parent().parent().replaceWith();

                $.ajax({
                    type: 'post',
                    url: '/data',
                    data: {
                        day: day,
                        from: 1,
                        to: 25
                    },

                    success: function(data) {
                        $('tr td').empty()
                        // console.log(data);
                        // console.log(day);
                        $.each(data, function(index, row) {
                            // console.log(row)
                            $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').empty()
                            let s = ''
                            if ($.isNumeric(row.class)) {
                                s += '<span>' + row.room_id + '</span>';
                                s += '<span>' + row.mssv + '</span>';
                                if (row.class == '0') {
                                    s += '<span class="text-danger">Processing</span>'
                                } else {
                                    s += '<span class="text-success">Accept</span>'
                                }
                            } else {
                                s += row.class
                            }
                            $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').append(s)
                        })
                    }

                })
            },
            error: function(xhr, status, error) {
                alert("There was an error with your request: " + error);
            }
        })
    }   
}

var $table = document.getElementById("myTable"),
    $n = 6,
    $rowCount = $table.rows.length,
    $firstRow = $table.rows[0].firstElementChild.tagName,
    $hasHead = ($firstRow === "TH"),
    $tr = [],
    $i, $ii, $j = ($hasHead) ? 1 : 0,
    $th = ($hasHead ? $table.rows[(0)].outerHTML : "");
var $pageCount = Math.ceil($rowCount / $n);
if ($pageCount > 1) {
    for ($i = $j, $ii = 0; $i < $rowCount; $i++, $ii++)
        $tr[$ii] = $table.rows[$i].outerHTML;
    document.getElementById("tables").insertAdjacentHTML("afterend", "<div id='buttons'></div");
    sort(1);
}

function sort($p) {
    var $rows = $th,
        $s = (($n * $p) - $n);
    for ($i = $s; $i < ($s + $n) && $i < $tr.length; $i++)
        $rows += $tr[$i];

    $table.innerHTML = $rows;
    document.getElementById("buttons").innerHTML = pageButtons($pageCount, $p);
    document.getElementById("id" + $p).setAttribute("class", "active");
}

function pageButtons($pCount, $cur) {
    $.ajax({
        type: 'post',
        url: '/data',
        data: {
            day: day,
            from: 1,
            to: 25
        },
        success: function(data) {
            // console.log(data);
            // console.log(day);
            $.each(data, function(index, row) {
                // console.log(row)
                $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').empty()
                let s = ''
                if ($.isNumeric(row.class)) {
                    s += '<span>' + row.room_id + '</span>';
                    s += '<span>' + row.mssv + '</span>';
                    if (row.class == '0') {
                        s += '<span class="text-danger">Processing</span>'
                    } else {
                        s += '<span class="text-success">Accept</span>'
                    }
                } else {
                    s += row.class
                }
                $('#' + row.name + ' td:nth-child(' + String(Number(row.slot) + 1) + ')').append(s)
            })
        }
    })

    // Pagination
    var $prevDis = ($cur == 1) ? "disabled" : "",
        $nextDis = ($cur == $pCount) ? "disabled" : "",
        $buttons = "<input type='button' value='<< Prev' onclick='sort(" + ($cur - 1) + ")' " + $prevDis + ">";
    for ($i = 1; $i <= $pCount; $i++)
        $buttons += "<input type='button' id='id" + $i + "'value='" + $i + "' onclick='sort(" + $i + ")'>";
    $buttons += "<input type='button' value='Next >>' onclick='sort(" + ($cur + 1) + ")' " + $nextDis + ">";
    return $buttons;
}