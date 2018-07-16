(function () {

    angular
            .module("articles")
            .controller("articlesCtrl", ArticlesController);


    ArticlesController.$inject = ['articlemanager'];

    function ArticlesController(articlemanager) 
    {

        var vm = this;

        vm.articlemanager = articlemanager; // Attaching the articlemanager object to the view model
        vm.articleSubmitted = articleSubmitted;
        vm.currentArticle = {
            title:'',
            tags:'',
            category:'',
            description:''
        };
        vm.validateSubmission = validateSubmission;
        vm.postArticle = postArticle;
        vm.error = false;
        vm.errorMsg = "";

        function articleSubmitted()
        {
            vm.error = false;
            console.log(vm.currentArticle);
            if(vm.validateSubmission())
            {
                vm.postArticle();
            }
        }

        function validateSubmission()
        {
            console.log('validating submission');
            if(vm.currentArticle.title.trim() == "")
            {
                vm.errorMsg += "Title is mandatory.\n";
                vm.error = true;
            }
            if(vm.currentArticle.tags.trim() == "")
            {
                vm.errorMsg += "Tags are mandatory.\n";
                vm.error = true;
            }
            if(vm.currentArticle.category.trim() == "")
            {
                vm.errorMsg += "Category is mandatory.\n";
                vm.error = true;
            }
            if(vm.currentArticle.description.trim() == "")
            {
                vm.errorMsg += "Description is mandatory.\n";
                vm.error = true;
            }
            if(vm.error==false)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        function postArticle()
        {
            console.log('posting article');
            var json =[];
            var tags = vm.currentArticle.tags;
            var toSplit = tags.split(",");
            for (var i = 0; i < toSplit.length; i++) {
                json.push(toSplit[i]);
            }
            vm.currentArticle.tags = json;
            vm.articlemanager.addArticle(vm.currentArticle);
            vm.currentArticle = {
                title:'',
                tags:'',
                category:'',
                description:''
            };
        }
    }


})();

