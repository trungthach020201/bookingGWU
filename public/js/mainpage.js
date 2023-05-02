// User
function getBookingId(getRoom, getSlot) {
    var room = document.getElementById('room')
    room.value = getRoom

    var slot = document.getElementById('slot')
    slot.value = getSlot

    var getDate = document.getElementById("change_day").value.split(" ")[1]
    document.getElementById('day').value = getDate;
}

function checkLogin() {
    window.location.href = '/login'
}

function sortByDay() {

    var day = document.getElementById("change_day").value
    window.location.href = '?day=' + day


}

function checkSubmit() {
    if (document.getElementById('txtReason').value == "") {
        alert("Opps");
    } else {
        let room = document.getElementById('room').value
        let slot = document.getElementById('slot').value
        $('#' + slot + room).replaceWith('<div class="loader"><div class="dot dot-1"></div><div class="dot dot-2"></div><div class="dot dot-3"></div><div class="dot dot-4"></div><div class="dot dot-5"></div></div>');
        $.ajax({
            type: 'post',
            url: '/form',
            data: {
                room: room,
                slot: slot,
                day: document.getElementById('day').value,
                reason: document.getElementById('txtReason').value
            },
            success: function() {
                window.location.reload();
            }
        })
    }

}

setTimeout(Refresh, 3000)

function Refresh() {
    var el = document.getElementById('success')
    $(el).closest('#success').css('background', '#d1e7dd')
    $(el)
        .closest('#success')
        .fadeOut(800, function() {
            $('#success').remove()
        })
}

// Pagination
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
    var $prevDis = ($cur == 1) ? "disabled" : "",
        $nextDis = ($cur == $pCount) ? "disabled" : "",
        $buttons = "<input type='button' value='<< Prev' onclick='sort(" + ($cur - 1) + ")' " + $prevDis + ">";
    for ($i = 1; $i <= $pCount; $i++)
        $buttons += "<input type='button' id='id" + $i + "'value='" + $i + "' onclick='sort(" + $i + ")'>";
    $buttons += "<input type='button' value='Next >>' onclick='sort(" + ($cur + 1) + ")' " + $nextDis + ">";
    return $buttons;
}