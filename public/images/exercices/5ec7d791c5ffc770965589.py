liste=[]
n=int(input("Entrez le nombre d'éléments de la liste: "))
i=1
while i<=n:
    a=int(input("Entrez l'élément {}: ".format(i)))
    liste.append(a)
    i=i+1
print("Voici la liste: ",liste)
print("Voici les entiers pairs de la liste:",end=" ")
for a in liste:
    if a%2==0:
        print(a,end=" ")