document.getElementById('pick_form').addEventListener('submit', (e)=>{
    e.preventDefault();
    const errorTxt = document.getElementById('pick_error');
    errorTxt.style.display = 'none';
    const value = document.getElementById('pick_select');
    if(value.value == ""){
        errorTxt.innerText = 'Select a learner and course.';
        errorTxt.style.display = 'block';
    } else {
        const arrayTxt = value.value.split("-");
        window.location.href = `./admin_user.php?uid=${arrayTxt[0]}&cid=${arrayTxt[1]}`;
    }
})