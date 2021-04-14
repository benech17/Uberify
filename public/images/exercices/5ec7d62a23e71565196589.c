#include<stdio.h>
#include<stdlib.h>
int main()
{
    int a,b,c;
    float moy;
    printf("Donnez 3 entiers:\n");
    scanf("%d%d%d",&a,&b,&c);
    moy=(float)(a+b+c)/3;
    printf("La moyenne de ces trois entiers est: %.2f\n",moy);
    system("pause");
    return 0;
}