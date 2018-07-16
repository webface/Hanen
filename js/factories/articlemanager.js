/*
 * IIFE to avoid global namespace pollution and keep code safe.
 */
(function () {


    angular
            .module("articles")
            .factory("articlemanager", ArticleManager);

    /*
     * dependency injection as seen in all the controllers. See comments 
     * there for a deeper explaination of dependency injection
     */
    ArticleManager.$inject = ['$http',  '$httpParamSerializer'];

    /*
     * function definition for the factory
     */
    function ArticleManager($http, $httpParamSerializer)
    {
        var articlesManager ={
            articles_data:[
                {
                    title:'Bigfoot Afoot',
                    tags:['True stories', 'forests'],
                    description:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dapibus mi at nulla finibus pretium. Curabitur auctor lacus vitae varius viverra. Sed pharetra varius sapien, id hendrerit ipsum lacinia placerat. Morbi vitae neque ipsum. Curabitur sem neque, iaculis vel nisl a, condimentum commodo arcu. Morbi nec urna efficitur, tempor nisl a, aliquet felis. Ut non est massa. Proin eget purus est. Integer eu nisi sed nisi maximus sodales. Duis finibus turpis a velit consectetur, luctus eleifend metus scelerisque. Pellentesque suscipit iaculis purus vel convallis.",
                    category:'Bigfoot'
                },
                {
                    title:'Lockness Sighted!',
                    tags:['Sightings', 'Lakes'],
                    description:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dapibus mi at nulla finibus pretium. Curabitur auctor lacus vitae varius viverra. Sed pharetra varius sapien, id hendrerit ipsum lacinia placerat. Morbi vitae neque ipsum. Curabitur sem neque, iaculis vel nisl a, condimentum commodo arcu. Morbi nec urna efficitur, tempor nisl a, aliquet felis. Ut non est massa. Proin eget purus est. Integer eu nisi sed nisi maximus sodales. Duis finibus turpis a velit consectetur, luctus eleifend metus scelerisque. Pellentesque suscipit iaculis purus vel convallis.",
                    category:'Nessy'
                },
                {
                    title:'Jacktalopes Everywhere',
                    tags:['Rabbits', 'cities'],
                    description:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus dapibus mi at nulla finibus pretium. Curabitur auctor lacus vitae varius viverra. Sed pharetra varius sapien, id hendrerit ipsum lacinia placerat. Morbi vitae neque ipsum. Curabitur sem neque, iaculis vel nisl a, condimentum commodo arcu. Morbi nec urna efficitur, tempor nisl a, aliquet felis. Ut non est massa. Proin eget purus est. Integer eu nisi sed nisi maximus sodales. Duis finibus turpis a velit consectetur, luctus eleifend metus scelerisque. Pellentesque suscipit iaculis purus vel convallis.",
                    category:'Jacktalope'
                }
            ],
            addArticle:addArticle,
            articles :(localStorage.getItem('articles')!==null) ? JSON.parse(localStorage.getItem('articles')): []
        };
        return articlesManager;

        function addArticle(article)
        {
            articlesManager.articles.push(article);
            localStorage.setItem('articles', JSON.stringify(articlesManager.articles));
        }
        
    }

})();
