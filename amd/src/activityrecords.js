function getrecord(id){
    const inputArray = [
        'apprentice',
        'reviewdate',
        'standard',
        'eors',
        'coach',
        'morm',
        'coursep',
        'courseep',
        'otjhc',
        'otjhe',
        'nextdate',
        'remotef2f',
        'hands',
        'eandd',
        'iaag'
    ];
    const innerArray = [
        'coursecomment',
        'otjhcomment',
        'recap',
        'recapimpact',
        'details',
        'detailsmod',
        'impact',
        'mathtoday',
        'mathnext',
        'engtoday',
        'engnext',
        'aln',
        'coachfeed',
        'safeguard',
        'agreedact',
        'apprencom',
        'activityrecord_title'
    ];
    const srcArray = [
        'filesrc'
    ];
    const displayArray = [
        'ar_sign_div'
    ];
    const signArray = [
        'learnsigndate',
        'coachsigndate'
    ];
    const imgsrcArray = [
        'coachsignimg',
        'learnsignimg'
    ];
    inputArray.forEach(function(item){
        $(`#${item}`)[0].value = '';
    });
    innerArray.forEach(function(item){
        $(`#${item}`)[0].innerText = '';
    });
    srcArray.forEach(function(item){
        $(`#${item}`)[0].src = '';
    });
    displayArray.forEach(function(item){
        $(`#${item}`)[0].style.display = 'none';
    });
    signArray.forEach(function(item){
        $(`#${item}`)[0].value = '';
        $(`#${item}`)[0].style.display = 'none';
    });
    imgsrcArray.forEach(function(item){
        $(`#${item}`)[0].src = '';
        $(`#${item}`)[0].style.display = 'none';
    });
    const contentDiv = $(`#activityrecord_content_div`)[0];
    contentDiv.style.display = 'none';
    const errorTxt = $(`#get_error`)[0];
    errorTxt.style.display = 'none';
    $(`#ar_sign_div`)[0].style.display = 'none';
    let fsTotal = [0,0];
    const params = `id=${id}`;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/activityrecords_getrecord.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            let text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = text['error'];
                errorTxt.style.display = 'block';
            } else{
                if(text['return']){
                    text['return'].forEach(function(item){
                        if(inputArray.includes(item[0])){
                            $(`#${item[0]}`)[0].value = item[1];
                        } else if(innerArray.includes(item[0])){
                            $(`#${item[0]}`)[0].innerHTML = item[1];
                            if(['mathtoday','mathnext'].includes(item[0]) && item[1] != ''){
                                fsTotal[0]++;
                            } else if(['engtoday','engnext'].includes(item[0]) && item[1] != ''){
                                fsTotal[1]++;
                            }
                        } else if(srcArray.includes(item[0])){
                            $(`#${item[0]}`)[0].src = item[1];
                        } else if(displayArray.includes(item[0])){
                            $(`#${item[0]}`)[0].style.display = item[1];
                        } else if(signArray.includes(item[0])){
                            $(`#${item[0]}`)[0].value = item[1];
                            $(`#${item[0]}`)[0].style.display = 'block';
                        } else if(imgsrcArray.includes(item[0])){
                            $(`#${item[0]}`)[0].src = item[1];
                            $(`#${item[0]}`)[0].style.display = 'block';
                        } else if(item[0] == 'impact_required'){
                            $('#recapimpact')[0].required = true;
                        }
                    })
                    if(fsTotal[0] > 0 || fsTotal[1] > 0){
                        $(`#func_div`)[0].style.display = '';
                        let tmpVals = ['none','none',true];
                        if(fsTotal[0] > 0){
                            tmpVals = ['', 'block', false];
                        }
                        $(`#func_title0`)[0].style.display = tmpVals[0];
                        $(`#math_title`)[0].style.display = tmpVals[0];
                        $(`#mathtoday`)[0].style.display = tmpVals[1];
                        $(`#mathtoday`)[0].disabled = tmpVals[2];
                        $(`#mathnext`)[0].style.display = tmpVals[1];
                        $(`#mathnext`)[0].disabled = tmpVals[2];
                        tmpVals = ['none', 'none', true];
                        if(fsTotal[1] > 0){
                            tmpVals = ['', 'block', false];
                        }
                        $(`#func_title1`)[0].style.display = tmpVals[0];
                        $(`#eng_title`)[0].style.display = tmpVals[0];
                        $(`#engtoday`)[0].style.display = tmpVals[1];
                        $(`#engtoday`)[0].disabled = tmpVals[2];
                        $(`#engnext`)[0].style.display = tmpVals[1];
                        $(`#engnext`)[0].disabled = tmpVals[2];
                    }
                    contentDiv.style.display = 'block';
                }
            }
        } else {
            errorTxt.innerText = 'Connection error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send(params);
}
function delrecord(id){
    const modaldiv = $(`#modal_${id}`)[0];
    if(modaldiv.style.display == 'none'){
        modaldiv.style.display = 'block';
    } else if(modaldiv.style.display == 'block'){
        const modalError = $(`#modal_${id}_error`)[0];
        modalError.innerText = '';
        modalError.style.display = 'none';
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/activityrecords_delrecord.inc.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    modalError.innerText = text['error'];
                    modalError.style.display = 'block';
                } else {
                    if(text['return']){
                        window.location.reload();
                    } else {
                        modalError.innerText = 'Deletion error.';
                        modalError.style.display = 'block';
                    }
                }
            } else {
                modalError.innerText = 'Connection error.';
                modalError.style.display = 'block';
            }
        }
        xhr.send(`id=${id}`);
    }
}
function closedelrecord(id){
    $(`#modal_${id}`)[0].style.display = 'none';
}
$('#activityrecord_content_div')[0].addEventListener('submit', (e)=>{
    e.preventDefault();
    const idsArray = [
        'apprentice',
        'reviewdate',
        'standard',
        'eors',
        'coach',
        'morm',
        'coursep',
        'courseep',
        'coursecomment',
        'otjhc',
        'otjhe',
        'otjhcomment',
        'recap',
        'recapimpact',
        'details',
        'detailsmod',
        'impact',
        'mathtoday',
        'mathnext',
        'engtoday',
        'engnext',
        'aln',
        'coachfeed',
        'safeguard',
        'agreedact',
        'apprencom',
        'nextdate',
        'remotef2f',
        'hands',
        'eandd',
        'iaag'
    ];
    const errorTxt = $('#ar_error')[0];
    errorTxt.style.display = 'none';
    let formData = new FormData();
    const file = $('#file')[0].files[0];
    if(file != null){
        formData.append('file', file);
    }
    $('#td_file')[0].style.background = '';
    idsArray.forEach(function(arr){
        formData.append(arr, $(`#${arr}`)[0].value.replaceAll('&', '($)'));
        $(`#td_${arr}`)[0].style.background = '';
    });
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/activityrecords_editrecord.inc.php', true);
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = 'Invalid values: ';
                text['error'].forEach(function(item){
                    if(idsArray.includes(item[0]) || item[0] == 'file'){
                        $(`#td_${item[0]}`)[0].style.background = 'red';
                        errorTxt.innerText += item[1] + '|';
                    }
                });
                errorTxt.style.display = 'block';
            } else if(text['return']){
                window.location.reload();
            } else {
                errorTxt.innerText = 'Submit error.';
                errorTxt.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection Error.';
            errorTxt.style.display = 'block';
        }
    }
    xhr.send(formData);
});