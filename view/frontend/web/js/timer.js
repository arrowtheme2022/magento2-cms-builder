 /**
 * Contus Support Interactive.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file PRICE COUNTDOWN-LICENSE.txt.
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento 1.4.x and 1.5.x COMMUNITY edition
 * Contus Support does not guarantee correct work of this package
 * on any other Magento edition except Magento 1.4.x and 1.5.x COMMUNITY edition.
 * =================================================================
 */

        if (typeof(BackColor)=="undefined")
            BackColor = "white";
        if (typeof(ForeColor)=="undefined")
            ForeColor= "black";
        if (typeof(DisplayFormat)=="undefined")
            DisplayFormat = "<span class='days'>%%D%%</span><span class='hours'>%%H%%</span><span class='mins'>%%M%%</span><span class='secs'>%%S%%</span>";
        if (typeof(CountActive)=="undefined")
            CountActive = true;
        if (typeof(FinishMessage)=="undefined")
            FinishMessage = "";
        if (typeof(CountStepper)!="number")
            CountStepper = -1;
        if (typeof(LeadingZero)=="undefined")
            LeadingZero = true;
        CountStepper = Math.ceil(CountStepper);
        if (CountStepper == 0)
            CountActive = false;
        var SetTimeOutPeriod = (Math.abs(CountStepper)-1)*1000 + 990;
        function calcage(secs, num1, num2) {
            s = ((Math.floor(secs/num1)%num2)).toString();
            if (LeadingZero && s.length < 2)
                s = "0" + s;
            return "<b>" + s + "</b>";
        }
        function CountBack(secs,iid,j) {
            if (secs < 0) {
                document.getElementById(iid).innerHTML = FinishMessage;
                document.getElementById('caption'+j).style.display = "none";
                document.getElementById('heading'+j).style.display = "none";
                return;
            }
            DisplayStr = DisplayFormat.replace(/%%D%%/g, calcage(secs,86400,100000));
            DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs,3600,24));
            DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs,60,60));
            DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs,1,60));
            document.getElementById(iid).innerHTML = DisplayStr;
            if (CountActive)
                setTimeout(function(){CountBack((secs+CountStepper),iid,j)}, SetTimeOutPeriod);
        }
