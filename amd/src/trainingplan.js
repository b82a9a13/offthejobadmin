document.getElementById('plan_form').addEventListener('submit', (e)=>{
    e.preventDefault();
    const idsArray = [
        'name',
        'employer',
        'startdate',
        'planenddate',
        'lengthofprog',
        'otjh',
        'epao',
        'fundsource',
        'bksbrm',
        'bksbre',
        'learnstyle',
        'skillscanlr',
        'skillscaner',
        'apprenhpw',
        'weeksonprog',
        'annualleave',
        'hoursperweek',
        'aostrength',
        'ltgoals',
        'stgoals',
        'iaguide',
        'recopl',
        'addsa',
        'cl_daterequired',
        'cl_logrequired'
    ];
    if(document.getElementById('mathaed') !== null){
        idsArray.push(
            'mathfs',
            'mathlevel',
            'mathmod',
            'mathsd',
            'mathped',
            'engfs',
            'englevel',
            'engmod',
            'engsd',
            'engped',
            'mathaed',
            'mathaead',
            'engaed',
            'engaead'
        );
    }
    const classArray = [
        [
            'mod-m',
            'mod-psd',
            'mod-ped',
            'mod-mw',
            'mod-potjh',
            'mod-mod',
            'mod-rsd',
            'mod-red',
            'mod-otjt'
        ],
        [
            'pr-type',
            'pr-pr',
            'pr-ar'
        ]
    ];
    let params = '';
    idsArray.forEach(function(arr){
        console.log(document.getElementById(arr).value);
        params += `${arr}=${document.getElementById(arr).value.replaceAll('&','($)')}&`;
        document.getElementById('td_'+arr).style.background = '';
    });
    let total = 0;
    classArray[0].forEach(function(arr){
        const currentElement = document.querySelectorAll(`.${arr}`);
        const tdElement = document.querySelectorAll(`.td-${arr}`);
        for(let i = 0; i < currentElement.length; i++){
            params += `${arr}-${i}=${currentElement[i].value.replaceAll('&','($)')}&`;
            tdElement[i].style.background = '';
        }
        total = (total < currentElement.length) ? currentElement.length : total; 
    });
    params += `mod-total=${total}&`;
    total = 0;
    pos = 0;
    classArray[1].forEach(function(arr){
        const currentElement = document.querySelectorAll(`.${arr}`);
        const tdElement = document.querySelectorAll(`.td-${arr}`);
        for(let i = 0; i < currentElement.length; i++){
            params += `${arr}-${i}=${currentElement[i].value}&`;
            tdElement[i].style.background = '';
        }
        total = (total < currentElement.length) ? currentElement.length : total;
        pos++;
    })
    params += `pr-total=${total}`;
    const errorTxt = document.getElementById('tp_error');
    errorTxt.style.display = 'none';
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './classes/inc/trainingplan.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorTxt.innerText = 'Invalid values: ';
                text['error'].forEach(function(item){
                    if(idsArray.includes(item[0])){
                        document.getElementById("td_"+item[0]).style.background = 'red';
                    } else if(classArray[0].includes(item[0])){
                        document.querySelectorAll(".td-"+item[0])[item[2]].style.background = 'red';
                    } else if (classArray[1].includes(item[0])){
                        document.querySelectorAll(".td-"+item[0])[item[2]].style.background = 'red';
                    }
                    errorTxt.innerText += item[1] + '|';
                });
                errorTxt.style.display = 'block';
            } else if(text['return']){
                window.location.reload();
            } else {
                errorTxt.innerText = 'Submit error.'; 
                errorTxt.style.display = 'block';
            }
        } else {
            errorTxt.innerText = 'Connection error.'; 
            errorTxt.style.display = 'block';
        }
    }
    xhr.send(params);
});
function addprRecord(){
    const html = "<td class='td-pr-type'><select class='w-100 pr-type' required><option disabled value='' selected>Learner/Employer</option><option value='Learner'>Learner</option><option value='Employer'>Employer</option></select></td><td class='td-pr-pr'><input class='w-100 pr-pr' type='date' required></td><td class='td-pr-ar'><input class='w-100 pr-ar' type='date'></td>";
    const tr = document.createElement('tr');
    tr.className = 'tr-td-pr tr-td-pr-new';
    tr.innerHTML = html;
    document.getElementById('pr_tbody').appendChild(tr);
    document.getElementById('pr_removerecord').disabled = false;
}
function removeprRecord(){
    const tr = document.querySelectorAll('.tr-td-pr-new');
    const length = tr.length;
    if(length > 0){
        tr[length - 1].remove();
        if(length == 1){
            document.getElementById('pr_removerecord').disabled = true;
        }
    }
}