def maxIndex(arg):
    ans=0
    for i in range(len(arg)):
        if(arg[i]>arg[ans]):
            ans=i
    return ans
